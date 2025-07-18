<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\DeviceHourlyRevenue;
use App\Models\HandResult;
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
                        'id' => $revenue->id,
                        'serial' => $revenue->device->serial ?? 'N/A',
                        'owner' => $revenue->device->user->username ?? 'N/A',
                        'date' => $revenue->date,
                        'hour' => $revenue->hour,
                        'total_money' => (float) $revenue->total_money,
                        'id_hand_result' => $revenue->id_hand_result ?? 'N/A',
                    ];
                })->sortBy('serial')->values();
        } else {
            // Lấy bản ghi mới nhất cho mỗi thiết bị từ DeviceHourlyRevenue
            $latestRevenues = DeviceHourlyRevenue::select('device_hourly_revenue.*')
                ->join(
                    DB::raw('(SELECT id_device, MAX(created_at) as max_created_at FROM device_hourly_revenue GROUP BY id_device) as latest'),
                    function ($join) {
                        $join->on('device_hourly_revenue.id_device', '=', 'latest.id_device')
                            ->on('device_hourly_revenue.created_at', '=', 'latest.max_created_at');
                    }
                )
                ->with(['device', 'device.user']) // Load quan hệ device và user
                ->get()
                ->map(function ($revenue) {
                    return (object) [
                        'id' => $revenue->id,
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
        $devices = Device::all();

        // Nếu là request Blade, trả về view
        return view('devices.index', compact('latestRevenues', 'devices'));
    }
    public function destroyRevenue(Request $request, $id)
    {
        $revenue = DeviceHourlyRevenue::find($id);

        if (!$revenue) {
            return response()->json([
                'success' => false,
                'message' => 'Bản ghi doanh thu không tồn tại'
            ], 404);
        }

        $revenue->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã xóa bản ghi doanh thu thành công'
            ]);
        }

        return redirect()->back()->with('success', 'Đã xóa bản ghi doanh thu thành công!');
    }

    public function compareMoneyByTimeRanges(Request $request)
{
    // ✅ Validate dữ liệu đầu vào
    $request->validate([
        'serial' => 'required|string|exists:devices,serial',
        'start_hand_result_id' => 'required|integer|exists:hand_results,id',
        'end_hand_result_id' => 'required|integer|exists:hand_results,id|gte:start_hand_result_id',
    ]);

    // ✅ Lấy tham số từ request
    $serial = $request->input('serial');
    $startHandResultId = (int) $request->input('start_hand_result_id');
    $endHandResultId = (int) $request->input('end_hand_result_id');

    // ✅ Lấy thiết bị từ serial
    $device = Device::where('serial', $serial)->first();
    if (!$device) {
        return response()->json([
            'success' => false,
            'message' => 'Thiết bị không tồn tại.'
        ], 404);
    }

    // ✅ Lấy khoảng thời gian từ HandResult
    $startHandResult = HandResult::find($startHandResultId);
    $endHandResult = HandResult::find($endHandResultId);

    if (!$startHandResult || !$endHandResult || 
        $startHandResult->id_device !== $device->id || 
        $endHandResult->id_device !== $device->id) {
        return response()->json([
            'success' => false,
            'message' => 'ID HandResult không hợp lệ hoặc không thuộc thiết bị được chọn.'
        ], 400);
    }

    $startTime = $startHandResult->created_at;
    $endTime = $endHandResult->created_at;

    // ✅ Lấy total_money từ DeviceHourlyRevenue trong khoảng thời gian
    $revenues = DeviceHourlyRevenue::where('id_device', $device->id)
        ->whereRaw("CONCAT(date, ' ', LPAD(hour, 2, '0'), ':00:00') BETWEEN ? AND ?", [$startTime, $endTime])
        ->with(['device', 'device.user'])
        ->get();

    $totalMoney = $revenues->sum('total_money');

    // ✅ Lấy tổng money từ HandResult theo ID
    $handResults = HandResult::where('id_device', $device->id)
        ->whereBetween('id', [$startHandResultId, $endHandResultId])
        ->get();

    $handResultMoney = $handResults->sum('money');

    // ✅ Dữ liệu trả về
    $data = [
        'start_hand_result_id' => $startHandResultId,
        'end_hand_result_id' => $endHandResultId,
        'start_time' => $startTime,
        'end_time' => $endTime,
        'device_hourly_revenue' => $revenues->map(function ($revenue) {
            return (object)[
                'serial' => $revenue->device->serial ?? 'N/A',
                'owner' => $revenue->device->user->username ?? 'N/A',
                'date' => $revenue->date,
                'hour' => $revenue->hour,
                'total_money' => (float) $revenue->total_money,
                'id_hand_result' => $revenue->id_hand_result ?? 'N/A',
            ];
        })->values(),
        'total_money' => (float) $totalMoney,
        'hand_result_total' => (float) $handResultMoney,
        'difference' => abs($totalMoney - $handResultMoney),
    ];

    // ✅ Trả về JSON nếu là API
    if ($request->expectsJson()) {
        return response()->json([
            'success' => true,
            'data' => $data,
            'filters' => [
                'serial' => $serial,
                'start_hand_result_id' => $startHandResultId,
                'end_hand_result_id' => $endHandResultId,
            ]
        ], 200);
    }

    // ✅ Trả về view nếu là request từ Blade
    $devices = Device::all();
    $latestRevenues = DeviceHourlyRevenue::with(['device', 'device.user'])
        ->orderBy('created_at', 'desc')
        ->get()
        ->map(function ($revenue) {
            return (object)[
                'id' => $revenue->id,
                'serial' => $revenue->device->serial ?? 'N/A',
                'owner' => $revenue->device->user->username ?? 'N/A',
                'date' => $revenue->date,
                'hour' => $revenue->hour,
                'total_money' => (float) $revenue->total_money,
                'id_hand_result' => $revenue->id_hand_result ?? 'N/A',
            ];
        })->values();

    return view('devices.index', compact('data', 'devices', 'latestRevenues'));
}

    public function getRevenuesBySerial(Request $request)
    {
        $serial = $request->query('serial');
        $revenues = DeviceHourlyRevenue::whereHas('device', function ($query) use ($serial) {
            $query->where('serial', $serial);
        })->select('date', 'hour', 'id_hand_result')->whereNotNull('id_hand_result')->distinct()->get();

        return response()->json([
            'success' => true,
            'revenues' => $revenues->map(function ($revenue) {
                return [
                    'date' => $revenue->date,
                    'hour' => $revenue->hour,
                    'id_hand_result' => $revenue->id_hand_result,
                ];
            })->values()
        ]);
    }
}
