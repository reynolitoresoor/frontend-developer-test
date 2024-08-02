<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('image');
            $table->string('title');
            $table->string('link');
            $table->date('date');
            $table->text('content');
            $table->string('status');
            $table->bigInteger('writer')->unsigned();
            $table->bigInteger('editor')->unsigned();
            $table->bigInteger('company')->unsigned();
            $table->foreign('writer')->references('id')->on('users');
            $table->foreign('editor')->references('id')->on('users');
            $table->foreign('company')->references('id')->on('companies');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
