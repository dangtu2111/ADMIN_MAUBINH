<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeviceStats extends Model
{
    protected $table = 'device_stats';
    protected $fillable = ['id_device', 'total_chi_wins', 'total_chi_losses', 'total_money', 'last_updated'];

    // Vô hiệu hóa cột updated_at
    public function getUpdatedAtColumn()
    {
        return null;
    }

    // Quan hệ: Một DeviceStats thuộc về một Device
    public function device()
    {
        return $this->belongsTo(Device::class, 'id_device');
    }
}
?>