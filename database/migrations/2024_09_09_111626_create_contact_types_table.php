<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contact_types', function (Blueprint $table) {
            $table->id();
            $table->string('type_english');
            $table->string('type_arabic');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_types');
    }
};
