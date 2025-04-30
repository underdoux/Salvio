<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('bpom_references', function (Blueprint $table) {
            $table->id();
            $table->string('bpom_code')->unique();
            $table->string('name');
            $table->string('category');
            $table->string('manufacturer')->nullable();
            $table->string('registration_holder')->nullable();
            $table->date('registration_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->text('composition')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('bpom_references');
    }
};
