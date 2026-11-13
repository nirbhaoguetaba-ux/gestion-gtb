<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicules_occasion', function (Blueprint $table) {
            $table->id();
            $table->string('reference', 20)->unique();
            $table->string('marque', 60);
            $table->string('modele', 80);
            $table->unsignedSmallInteger('annee');
            $table->unsignedInteger('kilometrage');
            $table->string('carburant', 30);
            $table->string('boite', 30);
            $table->decimal('prix', 10, 2);
            $table->string('image_url', 500);
            $table->text('description');
            $table->string('statut', 20)->default('disponible');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicules_occasion');
    }
};
