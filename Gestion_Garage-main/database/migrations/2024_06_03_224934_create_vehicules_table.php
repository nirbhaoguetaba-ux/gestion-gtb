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
        Schema::create('vehicules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id');
            $table->string('brand', 50);
            $table->string('model', 50);
            $table->integer('annee')->default(2022);
            $table->string('fuel_type', 20)->default('Essence');
            $table->string('registration_number', 25)->unique();
            $table->string('vin', 30)->nullable();
            $table->integer('kilometrage')->default(35000);
            $table->string('boite_vitesse', 25)->default('Manuelle');
            $table->string('couleur', 40)->default('Gris');
            $table->string('statut', 30)->default('en_attente'); // en_attente, en_reparation, pret, livre
            $table->string('photo')->nullable();
            $table->timestamps();

            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicules');
    }
};
