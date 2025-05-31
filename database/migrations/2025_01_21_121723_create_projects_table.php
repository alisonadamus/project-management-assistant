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
        Schema::create('projects', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('event_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignUlid('supervisor_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignUlid('assigned_to')->nullable()->references('id')->on('users')->cascadeOnDelete();
            $table->string('slug', 128)->unique();
            $table->string('name', 248)->unique();
            $table->string('appendix', 512)->nullable();
            $table->text('body')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
