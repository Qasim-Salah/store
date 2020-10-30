<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMainCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('main-categories', function (Blueprint $table) {
            $table->id();
            $table->string('translation_lang');
           $table->bigInteger('translation_of')->unsigned();
            $table->string('name');
            $table->string('slug')->nullable();
            $table->string('photo')->nullable();
            $table->tinyInteger('active')->default(1);
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
        Schema::dropIfExists('main-categories');
    }
}
