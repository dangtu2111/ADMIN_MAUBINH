<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeviceHourlyRevenue extends Model
{
    protected $table = 'device_hourly_revenue';
    protected $fillable = ['id_device', 'date', 'hour', 'total_money','id_hand_result'];

    // Vô hiệu hóa cột updated_at
    public function getUpdatedAtColumn()
    {
        return null;
    }

    // Quan hệ: Một DeviceHourlyRevenue thuộc về một Device
    public function device()
    {
        return $this->belongsTo(Device::class, 'id_device');
    }
}