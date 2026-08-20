<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('todos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('judul');
            $table->text('deskripsi')->nullable();
            $table->enum('priority', ['Low', 'Medium', 'High'])->default('Medium');
            $table->date('deadline')->nullable();
            $table->enum('status', ['To Do', 'Progress', 'Done'])->default('To Do');
            $table->string('color_badge')->default('#0d6efd');
            $table->integer('position')->default(0);
            $table->timestamps();
        });

        Schema::create('personal_kpis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->unsignedSmallInteger('tahun');
            $table->unsignedTinyInteger('bulan');
            $table->string('kategori');
            $table->integer('target')->default(0);
            $table->integer('realisasi')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('personal_kpis');
        Schema::dropIfExists('todos');
    }
};
