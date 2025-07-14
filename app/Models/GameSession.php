<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameSession extends Model
{
    protected $table = 'game_sessions';
    protected $fillable = ['id_device', 'first_hand', 'middle_hand', 'last_hand', 'created_at'];
    // Vô hiệu hóa cột updated_at
    public function getUpdatedAtColumn()
    {
        return null;
    }

    // Quan hệ: Một GameSession thuộc về một Device
    public function device()
    {
        return $this->belongsTo(Device::class, 'id_device');
    }

    // Quan hệ: Một GameSession có nhiều HandResult
    public function handResults()
    {
        return $this->hasMany(HandResult::class, 'id_session');
    }
}
?>