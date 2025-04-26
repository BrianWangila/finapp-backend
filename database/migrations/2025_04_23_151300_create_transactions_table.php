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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('card_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('from_account')->nullable();
            $table->string('name');
            $table->string('to_account')->nullable();
            $table->decimal('amount', 10, 2);
            $table->string('type_of_purchase')->nullable();
            $table->string('bank_name')->nullable();
            $table->boolean('negative')->default(false);
            $table->string('logo')->nullable();
            $table->timestamp('date')->nullable();
            $table->string('status')->default('pending');
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
