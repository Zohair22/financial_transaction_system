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
        Schema::create('plaid_transaction_syncs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plaid_item_id')->constrained('plaid_items')->cascadeOnDelete();
            $table->string('cursor')->nullable();
            $table->boolean('has_more')->default(false);
            $table->string('request_id')->nullable();
            $table->unsignedInteger('added_count')->default(0);
            $table->unsignedInteger('modified_count')->default(0);
            $table->unsignedInteger('removed_count')->default(0);
            $table->string('initiator')->nullable();
            $table->json('summary')->nullable();
            $table->timestamp('synced_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plaid_transaction_syncs');
    }
};
