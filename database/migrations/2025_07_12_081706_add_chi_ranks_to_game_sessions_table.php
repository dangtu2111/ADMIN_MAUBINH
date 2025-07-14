<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddChiRanksToGameSessionsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('hand_results', function (Blueprint $table) {
            $table->string('first_chi_rank')->nullable(false);
            $table->string('middle_chi_rank')->nullable(false);
            $table->string('last_chi_rank')->nullable(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hand_results', function (Blueprint $table) {
            $table->dropColumn(['first_chi_rank', 'middle_chi_rank', 'last_chi_rank']);
        });
    }
}
?>