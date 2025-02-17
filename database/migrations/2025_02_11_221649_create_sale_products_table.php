<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSaleProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sale_products', function (Blueprint $table) {
            $table->id();
            $table->string('product');
            $table->foreignId('sale_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('cut_id')->constrained('cuts')->onDelete('cascade');
            $table->string('width');
            $table->string('height');
            $table->decimal('base_price', 10, 2);
            $table->decimal('profit_percentage', 5, 2);
            $table->decimal('sale_price', 10, 2)->storedAs('base_price + (base_price * profit_percentage / 100)');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sale_products');
    }
}
