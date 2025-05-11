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
        if (DB::connection()->getDriverName() === 'sqlite') {
            // For SQLite, we need to recreate the table
            DB::statement('CREATE TEMPORARY TABLE bookings_backup AS SELECT * FROM bookings');
            Schema::drop('bookings');

            Schema::create('bookings', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('contact_id')->unsigned();
                $table->foreign('contact_id')->references('id')->on('contacts')->onDelete('cascade');
                $table->integer('waiter_id')->unsigned()->nullable();
                $table->integer('table_id')->unsigned()->nullable();
                $table->integer('correspondent_id')->nullable();
                $table->integer('business_id')->unsigned();
                $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
                $table->integer('location_id')->unsigned();
                $table->dateTime('booking_start');
                $table->dateTime('booking_end');
                $table->integer('created_by')->unsigned();
                $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
                $table->string('booking_status')->default('booked');
                $table->text('booking_note')->nullable();
                $table->timestamps();
            });

            // Get columns from backup table
            $columns = DB::select("PRAGMA table_info(bookings_backup)");
            $column_names = array_map(function($col) {
                return $col->name;
            }, $columns);
            
            // Build column list
            $column_list = implode(', ', $column_names);

            DB::statement("INSERT INTO bookings ($column_list) SELECT $column_list FROM bookings_backup");
            DB::statement('DROP TABLE bookings_backup');
        } else {
            DB::statement("ALTER TABLE bookings MODIFY booking_status VARCHAR(191) NOT NULL DEFAULT 'booked'");
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
            DB::statement('CREATE TEMPORARY TABLE bookings_backup AS SELECT * FROM bookings');
            Schema::drop('bookings');

            Schema::create('bookings', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('contact_id')->unsigned();
                $table->foreign('contact_id')->references('id')->on('contacts')->onDelete('cascade');
                $table->integer('waiter_id')->unsigned()->nullable();
                $table->integer('table_id')->unsigned()->nullable();
                $table->integer('correspondent_id')->nullable();
                $table->integer('business_id')->unsigned();
                $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
                $table->integer('location_id')->unsigned();
                $table->dateTime('booking_start');
                $table->dateTime('booking_end');
                $table->integer('created_by')->unsigned();
                $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
                $table->enum('booking_status', ['booked', 'completed', 'cancelled'])->default('booked');
                $table->text('booking_note')->nullable();
                $table->timestamps();
            });

            // Get columns from backup table
            $columns = DB::select("PRAGMA table_info(bookings_backup)");
            $column_names = array_map(function($col) {
                return $col->name;
            }, $columns);
            
            // Build column list
            $column_list = implode(', ', $column_names);

            DB::statement("INSERT INTO bookings ($column_list) SELECT $column_list FROM bookings_backup");
            DB::statement('DROP TABLE bookings_backup');
        } else {
            DB::statement("ALTER TABLE bookings MODIFY booking_status ENUM('booked', 'completed', 'cancelled') NOT NULL DEFAULT 'booked'");
        }
    }
};
