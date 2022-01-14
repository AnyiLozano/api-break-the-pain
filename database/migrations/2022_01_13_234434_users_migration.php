<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UsersMigration extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function(Blueprint $table){
            $table->increments('id');
            $table->string('uid');
            $table->string('fullname');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('phone');
            $table->unsignedInteger('pharmacy_id');
            $table->unsignedInteger('status_id');
            $table->timestamps();

            $table->foreign('pharmacy_id')->references('id')->on('pharmacies')->onUpdate('no action')->onDelete('no action');
            $table->foreign('status_id')->references('id')->on('statuses')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
