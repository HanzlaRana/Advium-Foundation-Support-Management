<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained()->onDelete('cascade');
            $table->foreignId('applicant_id')->constrained()->onDelete('cascade');
            $table->string('loan_number')->unique();
            $table->enum('asset_type', ['disabled_bike', 'rickshaw']);
            $table->decimal('total_amount', 10, 2);
            $table->decimal('down_payment', 10, 2)->default(0);
            $table->decimal('loan_amount', 10, 2);
            $table->decimal('monthly_installment', 10, 2);
            $table->integer('total_installments');
            $table->integer('paid_installments')->default(0);
            $table->decimal('total_paid', 10, 2)->default(0);
            $table->decimal('remaining_balance', 10, 2);
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', [
                'active',
                'completed',
                'late',
                'defaulted',
                'rescheduled'
            ])->default('active');
            $table->string('guarantor_name')->nullable();
            $table->string('guarantor_cnic')->nullable();
            $table->string('guarantor_phone')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};