<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    public function __construct()
    {
        // Yêu cầu người dùng phải đăng nhập và có vai trò admin
        $this->middleware(['auth', 'role:admin']);
    }

    /**
     * Hiển thị danh sách tất cả người dùng (Read - Index)
     */
    public function index()
    {
        $users = User::all();
        return response()->json(['users' => $users], 200);
        // Hoặc: return view('admin.users.index', compact('users')); // Nếu dùng giao diện web
    }

    /**
     * Hiển thị form tạo người dùng mới (Create - Form)
     */
    public function create()
    {
        return view('admin.users.create'); // Trả về view để tạo user (nếu dùng giao diện web)
    }

    /**
     * Lưu người dùng mới (Create - Store)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:50', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', Rule::in(['admin', 'user'])],
        ]);

        User::create([
            'username' => $validated['username'],
            'password_hash' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'created_at' => now(),
        ]);

        return response()->json(['message' => 'User created successfully'], 201);
        // Hoặc: return redirect()->route('admin.users.index')->with('success', 'User created successfully');
    }

    /**
     * Hiển thị thông tin một người dùng (Read - Show)
     */
    public function show($id)
    {
        $user = User::findOrFail($id);
        return response()->json(['user' => $user], 200);
        // Hoặc: return view('admin.users.show', compact('user')); // Nếu dùng giao diện web
    }

    /**
     * Hiển thị form chỉnh sửa người dùng (Update - Form)
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.edit', compact('user')); // Trả về view để chỉnh sửa user
    }

    /**
     * Cập nhật thông tin người dùng (Update)
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'username' => ['required', 'string', 'max:50', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8'],
            'role' => ['required', Rule::in(['admin', 'user'])],
        ]);

        $user->username = $validated['username'];
        $user->role = $validated['role'];

        if (!empty($validated['password'])) {
            $user->password_hash = Hash::make($validated['password']);
        }

        $user->save();

        return response()->json(['message' => 'User updated successfully'], 200);
        // Hoặc: return redirect()->route('admin.users.index')->with('success', 'User updated successfully');
    }

    /**
     * Xóa người dùng (Delete)
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Ngăn xóa tài khoản admin mặc định hoặc tài khoản đang đăng nhập
        if ($user->username === 'admin' || $user->id === Auth::id()) {
            return response()->json(['message' => 'Cannot delete this user'], 403);
            // Hoặc: return redirect()->route('admin.users.index')->with('error', 'Cannot delete this user');
        }

        $user->delete();

        return response()->json(['message' => 'User deleted successfully'], 200);
        // Hoặc: return redirect()->route('admin.users.index')->with('success', 'User deleted successfully');
    }
}
?>