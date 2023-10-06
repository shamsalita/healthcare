<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      /*   Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('hospital_name')->nullable();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('mobile_number');
            $table->string('profile_cover')->nullable();
            $table->string('profile')->nullable();
            $table->string('residential_country')->nullable();
            $table->string('residential_city')->nullable();
            $table->string('prefered_country')->nullable();
            $table->string('prefered_city')->nullable();
            $table->string('grade_primary')->nullable();
            $table->string('grade_secondary')->nullable();
            $table->text('about')->nullable();
            $table->boolean('user_status')->default('1')->comment('1 for hospital, 0 for in doctor');
            $table->boolean('status')->default('1')->comment('1 for active, 0 for in active');
            $table->rememberToken();
            $table->timestamps();
        }); */


         Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->string('phone_number');
            $table->text('address')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->string('cover_image')->nullable();
            $table->boolean('approval')->default('1')->comment('1 pending, 2 approve 3 reject');
            $table->boolean('user_status')->default('1')->comment('1 for hospital, 0 for in doctor');
            $table->boolean('flag')->default('1')->comment('1 for first time login, 0 for multi time login');
            $table->boolean('status')->default('1')->comment('1 for active, 0 for in active');
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
};
