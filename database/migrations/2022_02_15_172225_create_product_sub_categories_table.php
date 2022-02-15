<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductSubCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_sub_categories', function (Blueprint $table) {
            $table->id();
			$table->unsignedBigInteger('parent_id')->comment('Parent Category Id');
			$table->string('name');
			$table->string('image')->nullable();
            $table->timestamps();

			$table->foreign('parent_id')->on('product_categories')->references('id')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_sub_categories');
		Schema::dropForeign('product_sub_categories_parent_id_foreign');
    }
}
