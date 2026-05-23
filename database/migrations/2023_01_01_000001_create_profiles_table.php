<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Exécute les migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('gender', ['homme', 'femme', 'autre'])->nullable();
            $table->integer('age')->nullable();
            $table->string('religion')->nullable();
            $table->string('profession')->nullable();
            $table->text('bio')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->default('Côte d\'Ivoire');
            $table->string('education')->nullable();
            $table->string('marital_status')->nullable();
            $table->string('height')->nullable(); // cm
            $table->string('complexion')->nullable();
            $table->text('looking_for')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->timestamps();
            
            // Indexes
            $table->index('user_id');
            $table->index('gender');
            $table->index('age');
            $table->index('city');
            $table->index('is_verified');
        });
    }

    /**
     * Annule les migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('profiles');
    }
};