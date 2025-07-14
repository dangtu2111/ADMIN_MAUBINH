<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAllTables extends Migration
{
    public function up()
    {
        // Users
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username')->unique();
            $table->string('password_hash');
            $table->string('role')->default('user');
            $table->timestamp('created_at')->nullable();
        });

        // Devices
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->string('serial')->unique();
            $table->foreignId('id_user')->constrained('users')->onDelete('cascade');
            $table->string('status')->default('active');
            $table->timestamp('created_at')->nullable();
        });

        // Game Sessions
        Schema::create('game_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_device')->constrained('devices')->onDelete('cascade');
            $table->string('first_hand');
            $table->string('middle_hand');
            $table->string('last_hand');
            $table->timestamp('created_at')->nullable();
        });

        // Hand Results
        Schema::create('hand_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_device')->constrained('devices')->onDelete('cascade');
            $table->foreignId('id_session')->constrained('game_sessions')->onDelete('cascade');
            $table->string('hand_type')->nullable();
            $table->integer('chi_wins');
            $table->integer('chi_losses');
            $table->decimal('money', 15, 2);
            $table->timestamp('created_at')->nullable();
        });

        // Device Stats
        Schema::create('device_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_device')->constrained('devices')->onDelete('cascade');
            $table->integer('total_chi_wins')->default(0);
            $table->integer('total_chi_losses')->default(0);
            $table->decimal('total_money', 15, 2)->default(0);
            $table->timestamp('last_updated')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('device_stats');
        Schema::dropIfExists('hand_results');
        Schema::dropIfExists('game_sessions');
        Schema::dropIfExists('devices');
        Schema::dropIfExists('users');
    }
}
