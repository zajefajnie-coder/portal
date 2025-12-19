<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('portfolio_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('portfolio_id')->constrained()->onDelete('cascade');
            $table->string('image_path');
            $table->string('thumbnail_path')->nullable();
            $table->integer('order')->default(0);
            $table->text('alt_text')->nullable();
            $table->boolean('is_reported')->default(false);
            $table->text('report_reason')->nullable();
            $table->boolean('is_hidden')->default(false);
            $table->timestamps();
            
            $table->index(['portfolio_id', 'order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portfolio_images');
    }
};



