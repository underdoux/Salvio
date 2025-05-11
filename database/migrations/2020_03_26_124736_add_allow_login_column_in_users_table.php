<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            // For SQLite, we need to recreate the table
            DB::statement('CREATE TEMPORARY TABLE users_backup AS SELECT * FROM users');
            Schema::drop('users');
            
            Schema::create('users', function (Blueprint $table) {
                $table->increments('id');
                $table->string('surname');
                $table->string('first_name');
                $table->string('last_name')->nullable();
                $table->string('username')->nullable();
                $table->string('email');
                $table->string('password')->nullable();
                $table->char('language', 7)->default('en');
                $table->integer('contact_no')->nullable();
                $table->text('address')->nullable();
                $table->unsignedInteger('business_id')->nullable();
                $table->boolean('allow_login')->default(1);
                $table->boolean('status')->default(1);
                $table->boolean('is_cmmsn_agnt')->default(0);
                $table->decimal('cmmsn_percent', 4, 2)->default(0);
                $table->boolean('selected_contacts')->default(0);
                $table->date('dob')->nullable();
                $table->enum('marital_status', ['married', 'unmarried', 'divorced'])->nullable();
                $table->char('blood_group', 10)->nullable();
                $table->char('contact_number', 20)->nullable();
                $table->string('fb_link')->nullable();
                $table->string('twitter_link')->nullable();
                $table->string('social_media_1')->nullable();
                $table->string('social_media_2')->nullable();
                $table->text('permanent_address')->nullable();
                $table->text('current_address')->nullable();
                $table->string('guardian_name')->nullable();
                $table->string('custom_field_1')->nullable();
                $table->string('custom_field_2')->nullable();
                $table->string('custom_field_3')->nullable();
                $table->string('custom_field_4')->nullable();
                $table->longText('bank_details')->nullable();
                $table->string('id_proof_name')->nullable();
                $table->string('id_proof_number')->nullable();
                $table->enum('gender', ['male', 'female', 'others'])->nullable();
                $table->rememberToken();
                $table->timestamps();
            });

            // Get columns from backup table
            $columns = DB::select("PRAGMA table_info(users_backup)");
            $column_names = array_map(function($col) {
                return $col->name;
            }, $columns);
            $column_list = implode(', ', array_filter($column_names, function($col) {
                return $col != 'allow_login';
            }));

            // Restore the data
            DB::statement("INSERT INTO users ($column_list) SELECT $column_list FROM users_backup");
            DB::statement('DROP TABLE users_backup');
        } else {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('allow_login')->default(1)->after('business_id');
            });

            DB::statement('ALTER TABLE users MODIFY username VARCHAR(191) NULL');
            DB::statement('ALTER TABLE users MODIFY password VARCHAR(191) NULL');
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            // For SQLite, we need to recreate the table
            DB::statement('CREATE TEMPORARY TABLE users_backup AS SELECT * FROM users');
            Schema::drop('users');
            
            Schema::create('users', function (Blueprint $table) {
                $table->increments('id');
                $table->string('surname');
                $table->string('first_name');
                $table->string('last_name')->nullable();
                $table->string('username');
                $table->string('email');
                $table->string('password');
                $table->char('language', 7)->default('en');
                $table->integer('contact_no')->nullable();
                $table->text('address')->nullable();
                $table->unsignedInteger('business_id')->nullable();
                $table->boolean('status')->default(1);
                $table->boolean('is_cmmsn_agnt')->default(0);
                $table->decimal('cmmsn_percent', 4, 2)->default(0);
                $table->boolean('selected_contacts')->default(0);
                $table->date('dob')->nullable();
                $table->enum('marital_status', ['married', 'unmarried', 'divorced'])->nullable();
                $table->char('blood_group', 10)->nullable();
                $table->char('contact_number', 20)->nullable();
                $table->string('fb_link')->nullable();
                $table->string('twitter_link')->nullable();
                $table->string('social_media_1')->nullable();
                $table->string('social_media_2')->nullable();
                $table->text('permanent_address')->nullable();
                $table->text('current_address')->nullable();
                $table->string('guardian_name')->nullable();
                $table->string('custom_field_1')->nullable();
                $table->string('custom_field_2')->nullable();
                $table->string('custom_field_3')->nullable();
                $table->string('custom_field_4')->nullable();
                $table->longText('bank_details')->nullable();
                $table->string('id_proof_name')->nullable();
                $table->string('id_proof_number')->nullable();
                $table->enum('gender', ['male', 'female', 'others'])->nullable();
                $table->rememberToken();
                $table->timestamps();
            });

            // Get columns from backup table
            $columns = DB::select("PRAGMA table_info(users_backup)");
            $column_names = array_map(function($col) {
                return $col->name;
            }, $columns);
            $column_list = implode(', ', array_filter($column_names, function($col) {
                return $col != 'allow_login';
            }));

            // Restore the data
            DB::statement("INSERT INTO users ($column_list) SELECT $column_list FROM users_backup");
            DB::statement('DROP TABLE users_backup');
        } else {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('allow_login');
            });

            DB::statement('ALTER TABLE users MODIFY username VARCHAR(191) NOT NULL');
            DB::statement('ALTER TABLE users MODIFY password VARCHAR(191) NOT NULL');
        }
    }
};
