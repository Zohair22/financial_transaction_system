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
        Schema::create('plaid_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plaid_account_id')->constrained('plaid_accounts')->cascadeOnDelete();
            $table->string('transaction_id')->unique();
            $table->string('pending_transaction_id')->nullable();
            $table->decimal('amount', 20, 4);
            $table->string('iso_currency_code', 12)->nullable();
            $table->string('unofficial_currency_code', 12)->nullable();
            $table->date('date');
            $table->date('authorized_date')->nullable();
            $table->timestamp('datetime')->nullable();
            $table->timestamp('authorized_datetime')->nullable();
            $table->string('name');
            $table->string('merchant_name')->nullable();
            $table->string('merchant_entity_id')->nullable();
            $table->string('payment_channel')->nullable();
            $table->boolean('pending')->default(false);
            $table->string('transaction_code')->nullable();
            $table->string('transaction_type')->nullable();
            $table->string('account_owner')->nullable();
            $table->string('category_primary')->nullable();
            $table->string('category_detailed')->nullable();
            $table->string('personal_finance_confidence')->nullable();
            $table->string('personal_finance_primary')->nullable();
            $table->string('personal_finance_detailed')->nullable();
            $table->string('logo_url')->nullable();
            $table->string('website')->nullable();
            $table->json('counterparties')->nullable();
            $table->json('location')->nullable();
            $table->json('payment_meta')->nullable();
            $table->json('raw')->nullable();
            $table->timestamp('removed_at')->nullable();
            $table->timestamps();

            $table->index(['plaid_account_id', 'date']);
            $table->index('pending');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plaid_transactions');
    }
};
