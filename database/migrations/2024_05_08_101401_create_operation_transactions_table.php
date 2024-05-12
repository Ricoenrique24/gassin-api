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
        Schema::create('operation_transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_category_transaction');  
            $table->unsignedInteger('id_transaction');  
            $table->unsignedInteger('id_user');  
            $table->decimal('total_payment', 8, 2); // dapat diisi sampe 8 angka sebelum koma, 2 angka setelah koma
            $table->text('note')->nullable();
            $table->boolean('verified')->default(false); 
            $table->timestamps();
    
            // Foreign key 
            $table->foreign('id_category_transaction')->references('id')->on('category_transactions');  
            // $table->foreign('id_transaction')->references('id')->on('resupply_transactions');  
            // $table->foreign('id_transaction')->references('id')->on('purchase_transactions');  
            $table->foreign('id_user')->references('id')->on('users');  
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('operation_transactions');
    }
};
