<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\User\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('plaid_items', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->constrained()->cascadeOnDelete();
            $table->foreignId('plaid_link_token_id')
                ->nullable()
                ->constrained('plaid_link_tokens')
                ->nullOnDelete();
            $table->string('item_id')->unique();
            $table->string('institution_id')->nullable();
            $table->string('institution_name')->nullable();
            $table->string('webhook')->nullable();
            $table->json('available_products')->nullable();
            $table->json('billed_products')->nullable();
            $table->json('consented_products')->nullable();
            $table->json('error')->nullable();
            $table->string('update_type')->nullable();
            $table->string('status')->default('active');
            $table->timestamp('last_synced_at')->nullable();
            $table->string('cursor')->nullable();
            $table->timestamp('cursor_updated_at')->nullable();
            $table->text('encrypted_access_token');
            $table->string('access_token_hash')->unique();
            $table->string('request_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plaid_items');
    }
};
