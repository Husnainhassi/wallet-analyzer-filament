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
        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            $table->string('address')->nullable();
            $table->float('roi')->nullable();
            $table->float('winrate')->nullable();
            $table->enum('status', ['normal','approved','in_review','disqualified'])->default('normal');
            $table->timestamp('created_at')->useCurrent(); 
            $table->timestamp('updated_at')->nullable();   
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_data');
    }
};
