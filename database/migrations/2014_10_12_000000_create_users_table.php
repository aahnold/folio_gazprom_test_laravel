<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        $password = \Illuminate\Support\Str::random(16);

        Config::set('api.api_password', $password);
        file_put_contents(base_path('/config/api.php'), '<? return ' . var_export(Config::get('api'), true) . ';?>');

        $user = \App\User::create([
            'name' => 'frontend app',
            'email' => 'abc@def.ghi',
            'password' => Hash::make($password)
        ]);

        $user->markEmailAsVerified();
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
