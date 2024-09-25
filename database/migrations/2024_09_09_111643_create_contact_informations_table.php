<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contact_informations', function (Blueprint $table) {
            $table->id();
            $table->string('link');
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('contact_type_id')->constrained('contact_types');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_informations');
    }
};
