<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFavoritesTable extends Migration
{
    public function up(): void
    {
        Schema::create('favorites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); 
           
            $table->string('champion_id');  
            $table->timestamps();
            
            $table->unique(['user_id', 'champion_id']);
            
            //Foreign key
            $table->foreign('champion_id')->references('id')->on('champions')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('favorites');
    }
}
