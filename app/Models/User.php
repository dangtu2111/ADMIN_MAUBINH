<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;

    protected $table = 'users';
    protected $fillable = ['username', 'password_hash', 'role', 'created_at'];

    public function getUpdatedAtColumn()
    {
        return null;
    }

    public function getAuthIdentifierName()
    {
        return 'id';
    }

    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    public function devices()
    {
        return $this->hasMany(Device::class, 'id_user');
    }
}
?>