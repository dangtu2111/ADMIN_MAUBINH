<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    protected $table = 'devices';
    protected $fillable = ['serial', 'id_user','status', 'created_at'];

    // Vô hiệu hóa cột updated_at
    public function getUpdatedAtColumn()
    {
        return null;
    }

    // Quan hệ: Một Device thuộc về một User
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    // Quan hệ: Một Device có nhiều GameSession
    public function gameSessions()
    {
        return $this->hasMany(GameSession::class, 'id_device');
    }

    // Quan hệ: Một Device có nhiều HandResult
    public function handResults()
    {
        return $this->hasMany(HandResult::class, 'id_device');
    }

    // Quan hệ: Một Device có một DeviceStats
    public function deviceStats()
    {
        return $this->hasOne(DeviceStats::class, 'id_device');
    }
}
?>