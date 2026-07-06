<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('weather_data', function (Blueprint $table) {
            $table->decimal('predicted_temperature', 5, 2)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('source')->nullable();
            $table->timestamp('observed_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('weather_data', function (Blueprint $table) {
            $table->dropColumn([
                'predicted_temperature',
                'latitude',
                'longitude',
                'source',
                'observed_at',
            ]);
        });
    }
};
