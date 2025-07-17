<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\DeviceHourlyRevenue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller; // Thêm dòng này
use Illuminate\Support\Facades\DB;

class DeviceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {   
        if ($request->has('device_serial') && is_array($request->device_serial)) {
            // Lấy danh sách device_serial từ request
            $deviceSerials = $request->device_serial;

            // Lấy tất cả bản ghi từ DeviceHourlyRevenue cho các device_serial
            $latestRevenues = DeviceHourlyRevenue::whereHas('device', function ($query) use ($deviceSerials) {
                    $query->whereIn('serial', $deviceSerials); // Lọc theo device_serial
                })
                ->with(['device', 'device.user']) // Load quan hệ device và user
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($revenue) {
                    return (object) [
                        'serial' => $revenue->device->serial ?? 'N/A',
                        'owner' => $revenue->device->user->username ?? 'N/A',
                        'date' => $revenue->date,
                        'hour' => $revenue->hour,
                        'total_money' => (float) $revenue->total_money,
                        'id_hand_result' => $revenue->id_hand_result ?? 'N/A',
                    ];
                })->sortBy('serial')->values();
        }else{
            // Lấy bản ghi mới nhất cho mỗi thiết bị từ DeviceHourlyRevenue
            $latestRevenues = DeviceHourlyRevenue::select('device_hourly_revenue.*')
                ->join(DB::raw('(SELECT id_device, MAX(created_at) as max_created_at FROM device_hourly_revenue GROUP BY id_device) as latest'),
                    function ($join) {
                        $join->on('device_hourly_revenue.id_device', '=', 'latest.id_device')
                            ->on('device_hourly_revenue.created_at', '=', 'latest.max_created_at');
                    })
                ->with(['device', 'device.user']) // Load quan hệ device và user
                ->get()
                ->map(function ($revenue) {
                    return (object) [
                        'serial' => $revenue->device->serial ?? 'N/A',
                        'owner' => $revenue->device->user->username ?? 'N/A',
                        'date' => $revenue->date,
                        'hour' => $revenue->hour,
                        'total_money' => (float) $revenue->total_money, // Nhân với 1000
                        'id_hand_result' => $revenue->id_hand_result ?? 'N/A',
                    ];
                })->sortBy('serial')->values();
        }
        

        // Nếu là request API, trả về JSON
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $latestRevenues
            ], 200);
        }
        $devices=Device::all();

        // Nếu là request Blade, trả về view
        return view('devices.index', compact('latestRevenues','devices'));
    }
}
?>