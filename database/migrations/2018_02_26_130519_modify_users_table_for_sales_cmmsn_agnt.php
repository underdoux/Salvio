<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        // Create new users table with updated schema
        Schema::create('new_users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('surname', 10);
            $table->string('first_name');
            $table->string('last_name');
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->string('password');
            $table->char('contact_no', 15)->nullable();
            $table->text('address')->nullable();
            $table->integer('business_id')->unsigned()->nullable();
            $table->boolean('is_cmmsn_agnt')->default(0);
            $table->decimal('cmmsn_percent', 4, 2)->default(0);
            $table->string('language', 20)->nullable();
            $table->rememberToken();
            $table->timestamps();

            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
        });

        // Copy data from old table
        DB::statement('INSERT INTO new_users (
            id, surname, first_name, last_name, username, email, password, 
            business_id, language, remember_token, created_at, updated_at
        ) SELECT 
            id, surname, first_name, last_name, username, email, password, 
            business_id, language, remember_token, created_at, updated_at 
        FROM users');

        // Initialize new columns with default values
        DB::statement('UPDATE new_users SET contact_no = NULL, address = NULL, is_cmmsn_agnt = 0, cmmsn_percent = 0');

        // Drop old table and rename new table
        Schema::dropIfExists('users');
        Schema::rename('new_users', 'users');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
