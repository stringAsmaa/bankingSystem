<?php

use App\Enums\AccountStatus;
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
        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
            $table->string('account_number')->unique();
            $table->string('type');
            $table->string('status')->default(AccountStatus::ACTIVE->value);
            $table->decimal('balance', 15, 2)->default(0);
            $table->string('currency')->default('USD');
            // $table->decimal('interest_rate', 5, 2)->nullable();
            // $table->decimal('overdraft_limit', 15, 2)->nullable();
            $table->dateTime('opened_at')->useCurrent();
            $table->dateTime('closed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_accounts');
    }
};
