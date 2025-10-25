<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('user_product_groups', function (Blueprint $table) {
            $table->bigIncrements('group_id');
            $table->unsignedBigInteger('user_id')->index();
            $table->decimal('discount', 5, 2);
            $table->timestamps();

            // $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void {
        Schema::dropIfExists('user_product_groups');
    }
};
