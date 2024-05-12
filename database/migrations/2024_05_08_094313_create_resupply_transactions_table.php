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
        Schema::create('resupply_transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_store');
            $table->unsignedInteger('id_user');
            $table->integer('qty');
            $table->decimal('total_payment', 8, 2); // dapat diisi sampe 8 angka sebelum koma, 2 angka setelah koma
            $table->string('status');
            $table->text('note')->nullable();
            $table->timestamps();
    
            // Foreign key 
            $table->foreign('id_store')->references('id')->on('stores');
            $table->foreign('id_user')->references('id')->on('users');
            // $table->foreign('status')->references('status')->on('status_transactions');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resupply_transactions');
    }
};
