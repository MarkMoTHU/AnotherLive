<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRoomsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('uid')->unsigned()->unique()->index();
            $table->foreign('uid')->references('id')->on('users')->onDelete('cascade');
            $table->boolean('status')->default(0)->index();
            $table->string('streamKey');
            $table->string('title');
            $table->string('description');
            $table->integer('category_id')->index();
            $table->boolean('isStreaming')->default(false)->index();
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
        Schema::dropIfExists('rooms');
    }
}
