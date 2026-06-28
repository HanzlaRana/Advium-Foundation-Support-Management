<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('programs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->string('category');
            $table->enum('type', ['free', 'loan']);
            $table->string('icon')->nullable();
            $table->integer('total_helped')->default(0);
            $table->boolean('is_active')->default(true);
            $table->text('eligibility_criteria')->nullable();
            $table->json('required_documents')->nullable();
            $table->decimal('loan_amount', 10, 2)->nullable();
            $table->integer('loan_duration_months')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('programs');
    }
};