<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->tinyIncrements('id');
            $table->string('name', 50)->unique();
        });

        DB::table('roles')->insert([
            ['name' => 'photographer'],
            ['name' => 'model'],
            ['name' => 'makeup_artist'],
            ['name' => 'stylist'],
            ['name' => 'hairdresser'],
            ['name' => 'retoucher'],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};


