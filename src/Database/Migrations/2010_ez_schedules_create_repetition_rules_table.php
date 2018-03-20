<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RepetitionRulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('repetition_rules', function (Blueprint $table) {
            $table->increments('id');
            $table->text("repetition_strategy");
            $table->integer("priority")->default(0);
            $table->longText("item_model");
            $table->longText("params")->nullable();
            $table->longText("put_params")->nullable();
            $table->longText("put_closure")->nullable();
            $table->dateTime("starts_at")->nullable();
            $table->dateTime("ends_at")->nullable();
            $table->string("time");
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
        Schema::dropIfExists('schedule_items');
    }
}
