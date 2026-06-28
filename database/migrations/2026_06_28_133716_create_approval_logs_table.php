<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('approval_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('action', [
                'submitted',
                'assigned',
                'survey_submitted',
                'approved',
                'rejected',
                'on_hold',
                'sent_back',
                'distributed',
                'closed'
            ]);
            $table->enum('level', ['volunteer', 'admin', 'superadmin']);
            $table->text('remarks')->nullable();
            $table->string('from_status')->nullable();
            $table->string('to_status')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('approval_logs');
    }
};