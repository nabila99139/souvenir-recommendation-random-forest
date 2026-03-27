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
            // Add views column for analytics tracking
            $table->unsignedInteger('views')->default(0)->after('description');

            // Add price column for actual pricing
            $table->decimal('price', 10, 2)->nullable()->after('price_range');

            // Add image column (alternate name for image_path)
            $table->string('image')->nullable()->after('image_path');

            // Add indexes for performance
            $table->index('views');
            $table->index('price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('souvenirs', function (Blueprint $table) {
            // Drop the added columns and indexes
            $table->dropIndex(['views']);
            $table->dropIndex(['price']);
            $table->dropColumn(['views', 'price', 'image']);
        });
    }
};
