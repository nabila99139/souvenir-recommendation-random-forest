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
        Schema::table('souvenirs', function (Blueprint $table) {
            // Add seller_id column with foreign key relationship
            $table->foreignId('seller_id')->nullable()->constrained('users')->onDelete('cascade');

            // Add index for performance on seller-specific queries
            $table->index('seller_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('souvenirs', function (Blueprint $table) {
            // Drop the foreign key constraint and column
            $table->dropForeign(['seller_id']);
            $table->dropIndex(['seller_id']);
            $table->dropColumn('seller_id');
        });
    }
};
