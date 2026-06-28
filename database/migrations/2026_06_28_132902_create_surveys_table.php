<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('surveys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained()->onDelete('cascade');
            $table->foreignId('volunteer_id')->constrained('users')->onDelete('cascade');

            // House condition
            $table->enum('house_type', ['owned', 'rented', 'shared', 'homeless'])->nullable();
            $table->enum('house_condition', ['good', 'average', 'poor', 'very_poor'])->nullable();
            $table->integer('rooms')->nullable();

            // Utilities
            $table->boolean('has_electricity')->default(false);
            $table->boolean('has_gas')->default(false);
            $table->boolean('has_water')->default(false);
            $table->boolean('has_internet')->default(false);

            // Family details
            $table->integer('total_members')->nullable();
            $table->integer('earning_members')->nullable();
            $table->integer('school_going_children')->nullable();
            $table->decimal('total_monthly_income', 10, 2)->nullable();
            $table->decimal('total_monthly_expenses', 10, 2)->nullable();

            // Employment
            $table->enum('employment_status', [
                'employed',
                'self_employed',
                'unemployed',
                'disabled',
                'retired'
            ])->nullable();

            // Survey result
            $table->enum('eligibility_result', [
                'eligible',
                'conditionally_eligible',
                'not_eligible'
            ])->nullable();

            $table->text('notes')->nullable();
            $table->json('photos')->nullable();
            $table->timestamp('visited_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surveys');
    }
};