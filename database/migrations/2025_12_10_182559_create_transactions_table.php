<?php

use App\Modules\Transactions\Enums\TransactionStatus;
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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();

            // unique reference
            $table->uuid('transaction_reference')->unique();

            // accounts
            $table->foreignId('source_account_id')->constrained('bank_accounts')->onDelete('cascade');
            $table->foreignId('destination_account_id')->nullable()->constrained('bank_accounts')->nullOnDelete();

            // transaction info
            $table->string('transaction_type');
            $table->string('transaction_status')->default(TransactionStatus::PENDING->value);

            $table->decimal('transaction_amount', 15, 2);
            $table->string('transaction_currency', 10)->default('USD');

            // additional data
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable();

            // user actions
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by_user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->boolean('is_recurring')->default(false);
            $table->dateTime('next_run_at')->nullable();
            $table->string('frequency')->nullable();

            $table->timestamp('approved_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
