<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuditsTable extends Migration
{
    public function up()
    {
        Schema::create('audits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('action');
            $table->text('old_data')->nullable();
            $table->text('new_data');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('audits');
    }
}

