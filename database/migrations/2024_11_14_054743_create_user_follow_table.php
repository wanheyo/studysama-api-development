<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserFollowTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_follow', function (Blueprint $table) {
            $table->id();

            //both way is same, but the first one is more understandable
            // $table->unsignedBigInteger('user_follower_id');
            // $table->unsignedBigInteger('user_followed_id');
            
            // $table->foreign('user_follower_id')->references('id')->on('users')->onDelete('cascade');
            // $table->foreign('user_followed_id')->references('id')->on('users')->onDelete('cascade');

            //the second way is more easier
            $table->foreignId('user_follower_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('user_followed_id')->constrained('users')->onDelete('cascade');

            $table->integer('status')->default(1);
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
        Schema::dropIfExists('user_follow');
    }
}
