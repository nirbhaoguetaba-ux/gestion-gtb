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
        Schema::create('reparations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vehicule_id');
            $table->unsignedBigInteger('mecanique_id')->nullable();
            $table->string('titre', 150)->nullable();
            $table->string('status', 30)->default('en_attente'); // en_attente, diagnostic, en_cours, prete, terminee
            $table->string('priorite', 20)->default('normale'); // basse, normale, urgente
            $table->text('description')->nullable();
            $table->text('diagnostic')->nullable();
            $table->decimal('cout_pieces', 10, 2)->default(0);
            $table->decimal('cout_main_oeuvre', 10, 2)->default(0);
            $table->decimal('cout_total', 10, 2)->default(0);
            $table->boolean('notification_envoyee')->default(false);
            $table->timestamp('notification_envoyee_at')->nullable();
            $table->dateTime('date_debut')->nullable();
            $table->dateTime('date_fin')->nullable();
            $table->timestamps();

            $table->foreign('vehicule_id')->references('id')->on('vehicules')->onDelete('cascade');
            $table->foreign('mecanique_id')->references('id')->on('mecaniques')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reparations');
    }
};
