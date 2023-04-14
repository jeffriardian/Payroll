<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('employee_nik', 50);
            $table->bigInteger('user_group_id');
            $table->string('username')->unique();
            $table->string('email')->nullable();
            $table->string('password');
            $table->string('avatar')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->integer('account_status')->default(1);
            $table->rememberToken();
            $table->softDeletes();
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
}
