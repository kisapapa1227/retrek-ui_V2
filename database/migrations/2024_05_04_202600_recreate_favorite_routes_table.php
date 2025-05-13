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
        Schema::create('favorite_routes', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->string('smiles');
            $table->integer('route_id');
            $table->integer('route_num');
            $table->json('knowledge_weights');
            $table->boolean('save_tree');
            $table->float('expansion_num');
            $table->boolean('cum_prob_mod');
            $table->boolean('chem_axon');
            $table->float('selection_constant');
            $table->float('time_limit');

            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('favorite_routes');
    }
};
