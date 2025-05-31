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
        Schema::table('personal_access_tokens', function (Blueprint $table) {
            // Спочатку видаляємо індекси та зовнішні ключі
            $table->dropIndex(['tokenable_type', 'tokenable_id']);
            
            // Змінюємо тип колонки
            $table->string('tokenable_id', 26)->change();
            
            // Відновлюємо індекси
            $table->index(['tokenable_type', 'tokenable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('personal_access_tokens', function (Blueprint $table) {
            // Спочатку видаляємо індекси та зовнішні ключі
            $table->dropIndex(['tokenable_type', 'tokenable_id']);
            
            // Змінюємо тип колонки назад
            $table->unsignedBigInteger('tokenable_id')->change();
            
            // Відновлюємо індекси
            $table->index(['tokenable_type', 'tokenable_id']);
        });
    }
};
