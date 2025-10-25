<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('product_group_items', function (Blueprint $table) {
            $table->bigIncrements('item_id');
            $table->unsignedBigInteger('group_id')->index();
            $table->unsignedBigInteger('product_id')->index();
            $table->timestamps();

            $table->unique(['group_id','product_id']);

            // $table->foreign('group_id')->references('group_id')->on('user_product_groups')->onDelete('cascade');
            // $table->foreign('product_id')->references('product_id')->on('products')->onDelete('cascade');
        });
    }

    public function down(): void {
        Schema::dropIfExists('product_group_items');
    }
};
