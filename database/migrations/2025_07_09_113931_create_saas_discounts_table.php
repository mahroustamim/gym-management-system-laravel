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
        Schema::create('saas_discounts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['percent', 'fixed']);
            $table->decimal('value', 10, 2);
            $table->integer('max_usage');
            $table->integer('current_usage')->default(0);
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('saas_discounts');
    }
};
