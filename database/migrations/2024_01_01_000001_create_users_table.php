<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('profession')->nullable(); // model, photographer, makeup_artist, hairstylist, fashion_stylist
            $table->string('city')->nullable();
            $table->text('bio')->nullable();
            $table->string('phone')->nullable();
            $table->json('social_links')->nullable(); // {instagram, facebook, website, etc}
            $table->string('avatar')->nullable();
            $table->boolean('is_approved')->default(false);
            $table->boolean('is_banned')->default(false);
            $table->timestamp('banned_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};



