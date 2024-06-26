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
        Schema::create('purchase_transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_customer');
            $table->unsignedInteger('id_user');
            $table->integer('qty');
            $table->decimal('total_payment', 8, 2);
            $table->string('status');
            $table->text('note')->nullable();
            $table->timestamps();

            // Foreign key
            $table->foreign('id_customer')->references('id')->on('customers');
            $table->foreign('id_user')->references('id')->on('users');
            // $table->foreign('status')->references('status')->on('status_transactions');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_transactions');
    }
};
