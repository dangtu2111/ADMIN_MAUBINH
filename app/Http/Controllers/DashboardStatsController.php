<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;



class DashboardStatsController extends Controller
{
    public function getStats(): JsonResponse
    {
        // Ngày hiện tại và khoảng thời gian
        $now = Carbon::now(); // 2025-07-15
        $weekStart = $now->copy()->subDays(6); // 7 ngày trước (2025-07-09)
        $prevWeekStart = $weekStart->copy()->subDays(7); // 7 ngày trước đó (2025-07-02)
        $prevWeekEnd = $weekStart; // 2025-07-08
        $lastMonthStart = $now->copy()->subDays(30); // 30 ngày trước (2025-06-15)
        $lastMonthEnd = $now; // 2025-07-15
        $prevMonthStart = $lastMonthStart->copy()->subDays(30); // 2025-05-16
        $prevMonthEnd = $lastMonthStart; // 2025-06-15

        // Tổng số chi ăn 1 tuần (chi_wins - chi_losses)
        $weeklySpendingData = DB::table('hand_results')
            ->whereBetween('created_at', [$weekStart, $now])
            ->selectRaw('SUM(chi_wins) - SUM(chi_losses) as total')
            ->first();
        $weeklySpending = $weeklySpendingData ? (int) $weeklySpendingData->total : 0;

        $prevWeeklySpendingData = DB::table('hand_results')
            ->whereBetween('created_at', [$prevWeekStart, $prevWeekEnd])
            ->selectRaw('SUM(chi_wins) - SUM(chi_losses) as total')
            ->first();
        $prevWeeklySpending = $prevWeeklySpendingData ? (int) $prevWeeklySpendingData->total : 0;

        $weeklySpendingChange = $prevWeeklySpending != 0
            ? (($weeklySpending - $prevWeeklySpending) / abs($prevWeeklySpending)) * 100
            : ($weeklySpending > 0 ? 100 : 0);

        // Số thiết bị online
        $onlineDevices = DB::table('devices')
            ->where('status', 'active')
            ->count();

        $prevOnlineDevices = DB::table('devices')
            ->where('status', 'active')
            ->whereBetween('created_at', [$prevMonthStart, $prevMonthEnd])
            ->count();

        $onlineDevicesChange = $prevOnlineDevices > 0
            ? (($onlineDevices - $prevOnlineDevices) / $prevOnlineDevices) * 100
            : ($onlineDevices > 0 ? 100 : 0);

        // Tổng số chi ăn 1 ngày (chi_wins - chi_losses)
        $dailySpendingData = DB::table('hand_results')
            ->whereDate('created_at', $now->toDateString())
            ->selectRaw('SUM(chi_wins) - SUM(chi_losses) as total')
            ->first();
        $dailySpending = $dailySpendingData ? (int) $dailySpendingData->total : 0;

        $prevDailySpendingData = DB::table('hand_results')
            ->whereDate('created_at', $weekStart->toDateString())
            ->selectRaw('SUM(chi_wins) - SUM(chi_losses) as total')
            ->first();
        $prevDailySpending = $prevDailySpendingData ? (int) $prevDailySpendingData->total : 0;

        $dailySpendingChange = $prevDailySpending != 0
            ? (($dailySpending - $prevDailySpending) / abs($prevDailySpending)) * 100
            : ($dailySpending > 0 ? 100 : 0);

        // Lợi nhuận tuần
        $weeklyProfit = DB::table('hand_results')
            ->whereBetween('created_at', [$weekStart, $now])
            ->sum('money');

        $prevWeeklyProfit = DB::table('hand_results')
            ->whereBetween('created_at', [$prevMonthStart, $prevMonthEnd])
            ->sum('money');

        $weeklyProfitChange = $prevWeeklyProfit > 0
            ? (($weeklyProfit - $prevWeeklyProfit) / $prevWeeklyProfit) * 100
            : ($weeklyProfit > 0 ? 100 : 0);

        // Trả về dữ liệu JSON
        $stats = [
            'weeklySpending' => $weeklySpending,
            'weeklySpendingChange' => round($weeklySpendingChange, 2),
            'onlineDevices' => (int) $onlineDevices,
            'onlineDevicesChange' => round($onlineDevicesChange, 2),
            'dailySpending' => $dailySpending,
            'dailySpendingChange' => round($dailySpendingChange, 2),
            'weeklyProfit' => round($weeklyProfit, 2),
            'weeklyProfitChange' => round($weeklyProfitChange, 2)
        ];

        return response()->json($stats);
    }
    public function getChartData(): JsonResponse
    {
        // Xác định tuần hiện tại (từ thứ Hai đến Chủ Nhật)
        $now = Carbon::now(); // 2025-07-15
        $weekStart = $now->copy()->startOfWeek(Carbon::MONDAY); // 2025-07-14
        $weekEnd = $weekStart->copy()->endOfWeek(Carbon::SUNDAY); // 2025-07-20
        // Truy vấn tổng money theo ngày trong tuần
        $data = DB::table('hand_results')
            ->selectRaw('DATE(created_at) as date, SUM(money) as total_money')
            ->whereBetween('created_at', [$weekStart, $weekEnd])
            ->groupBy('date')
            ->get();
        // Khởi tạo mảng dữ liệu cho 7 ngày (Mon -> Sun)
        $dailyData = array_fill(0, 7, 0);
        $categories = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];

        // Ánh xạ dữ liệu vào mảng theo thứ tự ngày
        foreach ($data as $row) {
            $date = Carbon::parse($row->date);
            $dayIndex = $date->dayOfWeekIso - 1; // Carbon::MONDAY = 1, Sun = 7
            $dailyData[$dayIndex] = round($row->total_money, 2);
        }

        // Định dạng dữ liệu JSON
        $chartData = [
            'series' => [
                [
                    'name' => 'Actual',
                    'data' => $dailyData
                ]
            ],
            'categories' => $categories
        ];

        return response()->json($chartData);
    }
    public function lineChartData()
    {
        $user = Auth::user();
        $userId = $user->id;
        $isAdmin = $user->role === 'admin';

        $currentWeekStart = now()->startOfWeek()->startOfDay();
        $currentWeekEnd = now()->endOfWeek()->endOfDay();
        $previousWeekStart = now()->subWeek()->startOfWeek()->startOfDay();
        $previousWeekEnd = now()->subWeek()->endOfWeek()->endOfDay();

        $days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
        $currentWeekData = array_fill(0, 7, 0);
        $previousWeekData = array_fill(0, 7, 0);

        // Truy vấn current week
        $currentWeekQuery = DB::table('hand_results')
            ->selectRaw('DAYOFWEEK(created_at) as day_of_week, SUM(money) as total_money')
            ->whereBetween('created_at', [$currentWeekStart, $currentWeekEnd]);

        // Truy vấn previous week
        $previousWeekQuery = DB::table('hand_results')
            ->selectRaw('DAYOFWEEK(created_at) as day_of_week, SUM(money) as total_money')
            ->whereBetween('created_at', [$previousWeekStart, $previousWeekEnd]);

        // Nếu không phải admin, lọc theo id_user của thiết bị
        if (!$isAdmin) {
            $currentWeekQuery->join('devices', 'hand_results.device_id', '=', 'devices.id')
                ->where('devices.id_user', $userId);

            $previousWeekQuery->join('devices', 'hand_results.device_id', '=', 'devices.id')
                ->where('devices.id_user', $userId);
        }

        $currentWeekResults = $currentWeekQuery->groupBy('day_of_week')->get();
        $previousWeekResults = $previousWeekQuery->groupBy('day_of_week')->get();

        // Gán kết quả vào mảng đúng thứ
        foreach ($currentWeekResults as $row) {
            $index = ($row->day_of_week + 5) % 7; // 1=Sun → 6, 2=Mon → 0
            $currentWeekData[$index] = round($row->total_money, 2);
        }

        foreach ($previousWeekResults as $row) {
            $index = ($row->day_of_week + 5) % 7;
            $previousWeekData[$index] = round($row->total_money, 2);
        }

        return response()->json([
            'current_week' => $currentWeekData,
            'previous_week' => $previousWeekData
        ]);
    }
    public function getDeviceChiWinRate(): JsonResponse
    {
        // Lấy tổng chi_wins và chi_losses theo từng thiết bị
        $results = DB::table('hand_results')
            ->join('devices', 'hand_results.id_device', '=', 'devices.id')
            ->select(
                'devices.serial as device_name',
                DB::raw('SUM(chi_wins) as total_wins'),
                DB::raw('SUM(chi_losses) as total_losses')
            )
            ->groupBy('hand_results.id_device', 'devices.serial')
            ->get();

        $labels = [];
        $series = [];
        $totals = [];

        // Tính tổng chi_wins - chi_losses cho từng thiết bị
        foreach ($results as $row) {
            $netWin = $row->total_wins - $row->total_losses;

            if ($netWin > 0) {
                $labels[] = $row->device_name;
                $totals[] = $netWin;
            }
        }

        // Tổng tất cả netWin để tính phần trăm
        $grandTotal = array_sum($totals);

        // Tính phần trăm đóng góp của từng thiết bị
        foreach ($totals as $value) {
            $percent = $grandTotal > 0 ? round(($value / $grandTotal) * 100, 2) : 0;
            $series[] = $percent;
        }

        return response()->json([
            'labels' => $labels,
            'series' => $series
        ]);
    }
}
