<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnG2faToTableUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('google2fa_secrets', function (Blueprint $table) {
            
            $table->increments('id');
            $table->integer('user_id', false, 20);
            $table->tinyInteger('stat', false, 2);
            $table->string('secret', 30);
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
        Schema::dropIfExists('google2fa_secrets');
    }
}
