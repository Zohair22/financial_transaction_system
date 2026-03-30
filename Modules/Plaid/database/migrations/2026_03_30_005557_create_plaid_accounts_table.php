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
        Schema::create('plaid_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plaid_item_id')->constrained('plaid_items')->cascadeOnDelete();
            $table->string('account_id')->unique();
            $table->string('name');
            $table->string('official_name')->nullable();
            $table->string('mask', 16)->nullable();
            $table->string('type')->nullable();
            $table->string('subtype')->nullable();
            $table->string('holder_category')->nullable();
            $table->decimal('balance_available', 20, 4)->nullable();
            $table->decimal('balance_current', 20, 4)->nullable();
            $table->decimal('balance_limit', 20, 4)->nullable();
            $table->string('iso_currency_code', 12)->nullable();
            $table->string('unofficial_currency_code', 12)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_synced_at')->nullable();
            $table->json('raw_balances')->nullable();
            $table->json('raw')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plaid_accounts');
    }
};
