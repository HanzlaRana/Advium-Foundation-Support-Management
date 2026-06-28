<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('distributions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained()->onDelete('cascade');
            $table->foreignId('distributed_by')->constrained('users')->onDelete('cascade');
            $table->enum('delivery_method', ['physical', 'home_delivery', 'partner'])->default('physical');
            $table->date('scheduled_date')->nullable();
            $table->date('actual_date')->nullable();
            $table->string('location')->nullable();
            $table->string('recipient_name')->nullable();
            $table->string('recipient_cnic')->nullable();
            $table->string('recipient_phone')->nullable();
            $table->string('proof_photo')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['scheduled', 'completed', 'failed', 'cancelled'])->default('scheduled');
            $table->string('qr_code')->nullable()->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('distributions');
    }
};