<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('category_subject', function (Blueprint $table) {
            $table->foreignUlid('category_id')->constrained()->cascadeOnDelete();
            $table->foreignUlid('subject_id')->constrained()->cascadeOnDelete();
            $table->primary(['category_id', 'subject_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('category_subject');
    }
};
