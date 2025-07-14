<?php

   use Illuminate\Database\Migrations\Migration;
   use Illuminate\Database\Schema\Blueprint;
   use Illuminate\Support\Facades\Schema;

   class CreateSessionsTable extends Migration
   {
       public function up()
       {
           Schema::create('sessions', function (Blueprint $table) {
               $table->string('id')->primary(); // Primary key for session ID
               $table->foreignId('user_id')->nullable()->index()->constrained('users')->onDelete('cascade'); // Foreign key to users table
               $table->string('ip_address', 45)->nullable(); // IP address
               $table->text('user_agent')->nullable(); // User agent
               $table->longText('payload'); // Session payload
               $table->integer('last_activity')->index(); // Last activity timestamp
           });
       }

       public function down()
       {
           Schema::dropIfExists('sessions');
       }
   }
   ?>