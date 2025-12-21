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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // العميل
            $table->string('subject');
            $table->text('description')->nullable();
            $table->string('status')->default('open'); // open / in_progress / closed
            $table->string('priority')->default('normal'); // low / normal / high / urgent
            $table->foreignId('assigned_to_user_id')->nullable()->constrained('users')->nullOnDelete(); // الموظف المسؤول
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
