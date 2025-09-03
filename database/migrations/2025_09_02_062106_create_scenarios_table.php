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
        Schema::create('scenarios', function (Blueprint $table) {
            $table->id();
            $table->string('title',255)->nullable()->comment('シナリオ名');
            $table->string('url',255)->unique()->comment('URL');
            $table->text('body')->nullable()->comment('シナリオ本文');
            $table->integer('visible')->default(0)->comment('1:表示,0:非表示');
            $table->text('memo')->nullable()->comment('メモ');
            $table->string('image',255)->nullable()->comment('サムネイル');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scenarios');
    }
};
