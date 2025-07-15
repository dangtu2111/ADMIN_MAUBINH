<?php

namespace App\Http\Controllers;

use App\Models\HandResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller; // Thêm dòng này

class HandResultController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        // Số bản ghi mỗi trang (có thể tùy chỉnh qua query string)
        $perPage = $request->input('per_page', 10); // Mặc định 10 bản ghi/trang

        // Khởi tạo query cơ bản
        $query = HandResult::with('device');

        // Nếu không phải admin, chỉ hiển thị dữ liệu của chính user đó
        if (Auth::user()->role !== 'admin') {
            $query->whereHas('device', function ($q) {
                $q->where('id_user', Auth::id());
            });
        }

        // Lọc chi_wins nếu có tham số
        if ($request->has('chi_wins') && is_numeric($request->chi_wins)) {
            $query->where('chi_wins', '>=', $request->chi_wins);
        }

        // Lọc chi_losses nếu có tham số
        if ($request->has('chi_losses') && is_numeric($request->chi_losses)) {
            $query->where('chi_losses', '>=', $request->chi_losses);
        }
        $query->whereRaw('(chi_wins * 2* 0.98 - chi_losses * 2)  != money');

        // Áp dụng phân trang
        $handresults = $query->orderBy('created_at', 'desc')->paginate($perPage);

        // Trả về view với dữ liệu phân trang
        return view('listhand.index', compact('handresults'));
    }

    public function destroy(Request $request, $id)
    {
        // Tìm bản ghi HandResult
        $handResult = HandResult::with('device')->findOrFail($id);

        // Kiểm tra quyền: Chỉ admin hoặc người sở hữu thiết bị được xóa
        if (Auth::user()->role !== 'admin' && $handResult->device->id_user !== Auth::id()) {
            return redirect()->back()->with('error', 'Bạn không có quyền xóa bản ghi này.');
        }

        // Xóa bản ghi
        $handResult->delete();

        // Redirect về danh sách với thông báo thành công
        return redirect()->back()->with('success', 'Xóa bản ghi thành công.');
    }
    public function edit($id)
    {
        // Tìm bản ghi HandResult
        $handResult = HandResult::with('device','gameSession')->findOrFail($id);

        // Kiểm tra quyền: Chỉ admin hoặc người sở hữu thiết bị được chỉnh sửa
        if (Auth::user()->role !== 'admin' && $handResult->device->id_user !== Auth::id()) {
            return redirect()->route('listhand.index')->with('error', 'Bạn không có quyền chỉnh sửa bản ghi này.');
        }

        // Trả về view chỉnh sửa
        return view('listhand.edit', compact('handResult'));
    }

    public function update(Request $request, $id)
    {
        // Tìm bản ghi HandResult
        $handResult = HandResult::with('device')->findOrFail($id);

        // Kiểm tra quyền: Chỉ admin hoặc người sở hữu thiết bị được chỉnh sửa
        if (Auth::user()->role !== 'admin' && $handResult->device->id_user !== Auth::id()) {
            return redirect()->route('listhand.index')->with('error', 'Bạn không có quyền chỉnh sửa bản ghi này.');
        }

        // Xác thực dữ liệu đầu vào
        $request->validate([
            'hand_type' => 'nullable|string|max:255',
            'chi_wins' => 'required|integer|min:0',
            'chi_losses' => 'required|integer|min:0',
            'money' => 'required|numeric',
            'first_chi_rank' => 'required|string|max:255',
            'middle_chi_rank' => 'required|string|max:255',
            'last_chi_rank' => 'required|string|max:255',
        ]);

        // Cập nhật bản ghi
        $handResult->update([
            'hand_type' => $request->hand_type,
            'chi_wins' => $request->chi_wins,
            'chi_losses' => $request->chi_losses,
            'money' => $request->money,
            'first_chi_rank' => $request->first_chi_rank,
            'middle_chi_rank' => $request->middle_chi_rank,
            'last_chi_rank' => $request->last_chi_rank,
        ]);

        // Redirect về danh sách với thông báo thành công
        return redirect()->route('listhand.index')->with('success', 'Cập nhật bản ghi thành công.');
    }
}
?>