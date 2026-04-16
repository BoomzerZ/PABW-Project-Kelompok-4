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
        Schema::table('products', function (Blueprint $table) {
            $table->string('switch_type')->nullable();
            $table->integer('dpi')->nullable();
            $table->string('connectivity')->nullable();
            $table->string('sensor')->nullable();
            $table->string('weight')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['switch_type', 'dpi', 'connectivity', 'sensor', 'weight']);
        });
    }
};
