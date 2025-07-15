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
            return redirect()->route('listhand.index')->with('error', 'Bạn không có quyền xóa bản ghi này.');
        }

        // Xóa bản ghi
        $handResult->delete();

        // Redirect về danh sách với thông báo thành công
        return redirect()->route('listhand.index')->with('success', 'Xóa bản ghi thành công.');
    }
}
?>