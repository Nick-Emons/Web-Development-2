<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('champions', function (Blueprint $table) {
            $table->string('id')->primary(); 
            $table->string('name');  
            $table->string('title'); 
            $table->text('blurb');   
            $table->string('image');  
            $table->text('lore')->nullable(); 
            $table->json('tags')->nullable(); 
            $table->json('info')->nullable(); 
            $table->json('stats')->nullable(); 
            $table->json('spells')->nullable(); 
            $table->json('passive')->nullable(); 
            $table->json('skins')->nullable(); 
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('champions');
    }
};
