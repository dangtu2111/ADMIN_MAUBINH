<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeviceHourlyRevenueTable extends Migration
{
    public function up()
    {
        Schema::create('device_hourly_revenue', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_device')->constrained('devices')->onDelete('cascade');
            $table->date('date'); // Ngày của dữ liệu
            $table->unsignedTinyInteger('hour'); // Khung giờ (0-23)
            $table->decimal('total_money', 15, 2)->default(0); // Tổng tiền trong khung giờ
            $table->foreignId('id_hand_result')->nullable()->constrained('hand_results')->onDelete('set null');
            $table->timestamp('created_at')->useCurrent();
            $table->unique(['id_device', 'date', 'hour']); // Đảm bảo không trùng bản ghi
        });
    }

    public function down()
    {
        Schema::dropIfExists('device_hourly_revenue');
    }
}