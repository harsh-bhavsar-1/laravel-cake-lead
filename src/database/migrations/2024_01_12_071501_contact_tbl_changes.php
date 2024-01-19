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
        Schema::table('contact', function (Blueprint $table) {
            // Add your new columns here
            $table->text('cake_response')->nullable();
            $table->string('cake_status',10)->nullable();
            $table->string('cake_id',50)->nullable();
            $table->string('ckm_campaign_id',255)->nullable();
            $table->string('ckm_key',255)->nullable();
            $table->text('cake_errorcode')->nullable();
            // Add more columns if needed
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contact', function (Blueprint $table) {
            $table->dropColumn('cake_response');
            $table->dropColumn('cake_status');
            $table->dropColumn('cake_id');
            $table->dropColumn('ckm_campaign_id');
            $table->dropColumn('ckm_key');
            $table->dropColumn('cake_errorcode');
        });
    }
};
