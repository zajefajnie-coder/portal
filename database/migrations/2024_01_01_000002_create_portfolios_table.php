<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('portfolios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->boolean('is_public')->default(true);
            $table->integer('views')->default(0);
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['user_id', 'is_public']);
            $table->index('is_public');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portfolios');
    }
};



