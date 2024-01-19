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
        Schema::create('contact_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('contact_id');
            $table->tinyText('page')->nullable(true);
            $table->text('query_string')->nullable(true);
            $table->string('ckm_offer_id')->nullable(true);
            $table->string('oc')->nullable(true);
            $table->string('reqid')->nullable(true);
            $table->string('s1')->nullable(true);
            $table->string('s2')->nullable(true);
            $table->string('s3')->nullable(true);
            $table->string('subid')->nullable(true);
            $table->tinyText('referrer')->nullable(true);
            $table->string('submitted')->nullable(true);
            $table->string('tax_debt')->nullable(true);
            $table->string('neustar')->nullable(true);
            $table->text('neustar_disposition')->nullable(true);
            $table->string('melissa')->nullable(true);
            $table->string('cake_id')->nullable(true);
            $table->string('cake_status')->nullable(true);
            $table->text('cake_response')->nullable(true);
            $table->text('cake_errorcode')->nullable(true);
            $table->string('domain')->nullable(true);
            $table->string('opt_special_offers')->nullable(true);
            $table->string('opt_in')->nullable(true);
            $table->string('email_optin_offers')->nullable(true);
            //Remaing
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('contact_id')->references('id')->on('contact')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact_details');
    }
};
