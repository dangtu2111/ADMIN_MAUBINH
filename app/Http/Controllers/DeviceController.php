<?php

namespace App\Http\Controllers;

use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller; // Thêm dòng này

class DeviceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        if (Auth::user()->role === 'admin') {
            $devices = Device::with('deviceStats', 'user')->get();
        } else {
            $devices = Device::with('deviceStats', 'user')->where('id_user', Auth::id())->get();
        }

        return view('devices.index', compact('devices'));
    }
}
?>