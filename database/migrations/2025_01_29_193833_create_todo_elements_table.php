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
        Schema::create('todo_elements', function (Blueprint $table) {
            $table->id();
            $table->string('todo');
            $table->unsignedBigInteger('todo_list_id');
            $table->enum('done',['true','false']);

            $table->foreign('todo_list_id')->references('id')->on('todo_lists');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('todo_elements');
    }
};
