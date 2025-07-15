<?php
namespace App\Http\Controllers;

use App\Models\GameSession;
use App\Models\HandResult;
use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controller;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\RateLimiter;

class GameSessionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * Tạo mới game session
     */
    public function store(Request $request)
    {
        // Rate limiting: Giới hạn 10 requests/giây cho mỗi user
        $rateLimitKey = 'game_session_store_' . Auth::id();
        if (!RateLimiter::attempt($rateLimitKey, 10, function() {}, 1)) {
            return response()->json(['message' => 'Too many requests'], 429);
        }
       
        // Validate tối ưu với cached rules
        $validated = $request->validate([
            'serial' => ['required', 'string', 'max:255'],
            'first_hand' => ['required', 'string', 'max:255'],
            'middle_hand' => ['required', 'string', 'max:255'],
            'last_hand' => ['required', 'string', 'max:255'],
            'chi_wins' => ['required', 'integer', 'min:0'],
            'chi_losses' => ['required', 'integer', 'min:0'],
            'money' => ['required', 'numeric'],
            'hand_type' => ['required','string', 'max:30'],
            'first_chi_rank' =>['required', 'numeric', 'min:0'],
            'middle_chi_rank' => ['required', 'numeric', 'min:0'],
            'last_chi_rank' => ['required', 'numeric', 'min:0'],
        ], [
            'serial.required' => 'Device serial is required',
            'hand_type.in' => 'Invalid hand type provided',
            'middle_chi_rank.required_with' => 'Middle chi rank is required when middle hand is provided',
        ]);
        // Cache key với timeout ngắn hơn
        $cacheKey = 'device_serial_' . md5($validated['serial'] . '_' . Auth::id());
        $lock = Cache::lock($cacheKey . '_lock', 2); // Giảm timeout xuống 2 giây

        try {
            if (!$lock->get()) {
                return response()->json(['message' => 'Request is being processed, please try again'], 429);
            }
            
            // Tối ưu hóa lấy/tạo device với optimistic locking
            $device = Cache::lock($cacheKey, 10)->get(function () use ($validated) {
                $device = Device::where('serial', $validated['serial'])->first();

                if (!$device) {
                    DB::transaction(function () use ($validated, &$device) {
                        // Kiểm tra lại để tránh race condition
                        $device = Device::where('serial', $validated['serial'])->first();
                        if (!$device) {
                            $device = Device::create([
                                'serial' => $validated['serial'],
                                'id_user' => Auth::id(),
                                'status' => 'active',
                                'created_at' => now(),
                            ]);
                        }
                    });
                }

                return $device;
            });
            if ($device->status !== 'active') {
                return response()->json(['message' => 'Device is not active'], 403);
            }
            // Kiểm tra quyền sở hữu
            if (Auth::user()->role !== 'admin' && $device->id_user !== Auth::id()) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }
            // Transaction tối ưu với batch insert
            $response = DB::transaction(function () use ($validated, $device, $cacheKey) {
               
                $session = GameSession::create([
                    'id_device' => $device->id,
                    'first_hand' => $validated['first_hand'],
                    'middle_hand' => $validated['middle_hand'],
                    'last_hand' => $validated['last_hand'],
                    'created_at' => now(),
                ]);
                HandResult::create([
                    'id_device' => $device->id,
                    'id_session' => $session->id,
                    'hand_type' => $validated['hand_type'],
                    'chi_wins' => $validated['chi_wins'],
                    'chi_losses' => $validated['chi_losses'],
                    'money' => $validated['money'],
                    'first_chi_rank' => $validated['first_chi_rank'],
                    'middle_chi_rank' => $validated['middle_chi_rank'],
                    'last_chi_rank' => $validated['last_chi_rank'],
                    'created_at' => now(),
                ]);
                
                Cache::forget($cacheKey); // Xóa cache ngay sau khi tạo

                return response()->json([
                    'message' => 'Game session created successfully',
                    'data' => $session->only(['id', 'id_device', 'first_hand', 'middle_hand', 'last_hand'])
                ], 201);
            }, 2); // Giảm retry xuống 2 để tăng tốc độ

            return $response;
        } catch (\Exception $e) {
            // Logging tối giản để giảm I/O
            \Log::error('Game session creation failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);
            return response()->json(['message' => 'Failed to create game session'], 500);
        } finally {
            $lock->release(); // Giải phóng khóa
        }
    }
}
?>