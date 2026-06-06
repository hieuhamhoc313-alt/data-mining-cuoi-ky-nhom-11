<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->string('address', 500);
            $table->string('city', 100)->nullable();
            $table->string('district', 100)->nullable();
            $table->string('ward', 100)->nullable();
            $table->decimal('area', 10, 2);
            $table->decimal('frontage', 8, 2)->nullable();
            $table->decimal('access_road', 8, 2)->nullable();
            $table->enum('house_direction', [
                'Đông', 'Tây', 'Nam', 'Bắc',
                'Đông - Bắc', 'Đông - Nam', 'Tây - Bắc', 'Tây - Nam'
            ])->nullable();
            $table->enum('balcony_direction', [
                'Đông', 'Tây', 'Nam', 'Bắc',
                'Đông - Bắc', 'Đông - Nam', 'Tây - Bắc', 'Tây - Nam'
            ])->nullable();
            $table->integer('floors')->nullable();
            $table->integer('bedrooms')->nullable();
            $table->integer('bathrooms')->nullable();
            $table->enum('legal_status', [
                'Have certificate', 'Sale contract', 'Pending', 'Other'
            ])->nullable();
            $table->enum('furniture_state', ['Full', 'Basic', 'Empty'])->nullable();
            $table->decimal('price', 15, 2);
            $table->string('price_segment', 20)->nullable();
            $table->timestamps();

            $table->index('city');
            $table->index('legal_status');
            $table->index('furniture_state');
            $table->index('price_segment');
            $table->index('price');
            $table->index('area');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
