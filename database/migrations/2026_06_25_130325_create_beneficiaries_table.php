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
        Schema::create('beneficiaries', function (Blueprint $table) {
    $table->id();
    $table->string('beneficiary_code')->unique();
    $table->string('full_name');
    $table->string('cnic')->unique();
    $table->string('phone');
    $table->string('email')->nullable();
    $table->date('date_of_birth')->nullable();
    $table->enum('gender', ['Male', 'Female']);
    $table->text('address');
    $table->string('city')->nullable();
    $table->string('family_size')->nullable();
    $table->decimal('monthly_income', 12, 2)->nullable();
    $table->enum('status', ['Pending', 'Approved', 'Rejected'])->default('Pending');
    $table->text('notes')->nullable();
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('beneficiaries');
    }
};
