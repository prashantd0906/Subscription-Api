<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_promo_codes', function (Blueprint $table) {
            $table->unsignedBigInteger('subscription_id');
            $table->unsignedBigInteger('promo_code_id');
            $table->timestamp('used_at')->nullable();
            $table->timestamps();

            $table->primary(['subscription_id', 'promo_code_id']);
            $table->foreign('subscription_id')->references('id')->on('subscriptions')->onDelete('cascade');
            $table->foreign('promo_code_id')->references('id')->on('promo_codes')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_promo_codes');
    }
};
