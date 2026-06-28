<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('sku')->unique();
            $table->enum('category', [
                'grocery',
                'sewing_machine',
                'disabled_bike',
                'rickshaw',
                'education',
                'other'
            ]);
            $table->text('description')->nullable();
            $table->string('unit')->default('piece');
            $table->integer('quantity_in_stock')->default(0);
            $table->integer('quantity_reserved')->default(0);
            $table->integer('quantity_distributed')->default(0);
            $table->integer('reorder_level')->default(10);
            $table->decimal('unit_cost', 10, 2)->default(0);
            $table->date('expiry_date')->nullable();
            $table->string('supplier')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_items');
    }
};