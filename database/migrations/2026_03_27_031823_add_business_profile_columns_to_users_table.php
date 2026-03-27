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
        Schema::table('users', function (Blueprint $table) {
            // Business profile columns for sellers
            $table->string('business_name')->nullable()->after('authorized_by');
            $table->text('business_description')->nullable()->after('business_name');
            $table->string('business_address')->nullable()->after('business_description');
            $table->string('business_phone')->nullable()->after('business_address');
            $table->string('business_hours')->nullable()->after('business_phone');

            // Add indexes for performance
            $table->index('business_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop the business profile columns
            $table->dropIndex(['business_name']);
            $table->dropColumn([
                'business_name',
                'business_description',
                'business_address',
                'business_phone',
                'business_hours'
            ]);
        });
    }
};
