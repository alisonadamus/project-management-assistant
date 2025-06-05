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
        // Видаляємо існуючу таблицю та створюємо нову з правильними типами
        Schema::connection(config('webpush.database_connection'))->dropIfExists(config('webpush.table_name'));
        
        Schema::connection(config('webpush.database_connection'))->create(config('webpush.table_name'), function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('subscribable_type');
            $table->string('subscribable_id'); // Змінюємо на string для ULID
            $table->string('endpoint', 500);
            $table->string('public_key')->nullable();
            $table->string('auth_token')->nullable();
            $table->string('content_encoding')->nullable();
            $table->timestamps();
            
            // Індекси
            $table->index(['subscribable_type', 'subscribable_id']);
            $table->unique(['subscribable_type', 'subscribable_id', 'endpoint'], 'push_subscriptions_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Повертаємо до попереднього стану
        Schema::connection(config('webpush.database_connection'))->dropIfExists(config('webpush.table_name'));
        
        Schema::connection(config('webpush.database_connection'))->create(config('webpush.table_name'), function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->morphs('subscribable');
            $table->string('endpoint', 500)->unique();
            $table->string('public_key')->nullable();
            $table->string('auth_token')->nullable();
            $table->string('content_encoding')->nullable();
            $table->timestamps();
        });
    }
};
