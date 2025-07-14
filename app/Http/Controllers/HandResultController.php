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
}
?>