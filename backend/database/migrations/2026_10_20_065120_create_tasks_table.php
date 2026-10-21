<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->default(1)->comment('Placeholder user ID');
            $table->string('nama')->comment('Task name');
            $table->text('deskripsi')->nullable()->comment('Task description');
            $table->date('deadline')->comment('Task deadline');
            $table->enum('status', ['belum_dikerjakan', 'sedang_dikerjakan', 'selesai'])->default('belum_dikerjakan')->comment('Task status');
            $table->enum('prioritas', ['rendah', 'sedang', 'tinggi'])->comment('Task priority');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
