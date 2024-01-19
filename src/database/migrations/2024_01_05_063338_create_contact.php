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
        Schema::create('contact', function (Blueprint $table) {
            $table->id();
            $table->string('first_name', 50);
            $table->string('last_name', 50);
            $table->string('email_address', 50);
            $table->string('primary_phone', 50)->nullable(true);
            $table->string('alt_phone', 50)->nullable(true);
            $table->string('state', 50)->nullable(true);
            $table->string('zip_code', 50)->nullable(true);
            $table->string('enrolled_irs', 50)->nullable(true);
            $table->string('affid')->nullable(true);
            $table->integer('submit_attempts')->nullable(true);
            $table->tinyText('user_agent')->nullable(true);
            $table->ipAddress('ip_address')->nullable(true);
            $table->tinyText('geo_lookup')->nullable(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact');
    }
};
