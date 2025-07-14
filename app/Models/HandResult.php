<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HandResult extends Model
{
    protected $table = 'hand_results';
    protected $fillable = ['id_device', 'id_session', 'hand_type', 'chi_wins', 'chi_losses', 'money','first_chi_rank','middle_chi_rank','last_chi_rank', 'created_at'];

    // Vô hiệu hóa cột updated_at
    public function getUpdatedAtColumn()
    {
        return null;
    }

    // Quan hệ: Một HandResult thuộc về một Device
    public function device()
    {
        return $this->belongsTo(Device::class, 'id_device');
    }

    // Quan hệ: Một HandResult thuộc về một GameSession
    public function gameSession()
    {
        return $this->belongsTo(GameSession::class, 'id_session');
    }
   
}
?>