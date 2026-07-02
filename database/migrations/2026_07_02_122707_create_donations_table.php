<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('donations', function (Blueprint $table) {
            $table->id();
            $table->string('donor_name');
            $table->string('donor_phone')->nullable();
            $table->string('donor_email')->nullable();
            $table->enum('type', ['Cash', 'Zakat', 'Sadqa', 'In-Kind', 'Bank Transfer'])->default('Cash');
            $table->decimal('amount', 12, 2)->default(0);
            $table->date('date');
            $table->text('notes')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donations');
    }
};