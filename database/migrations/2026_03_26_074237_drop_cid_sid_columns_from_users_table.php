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
            // Drop cid and sid columns as they are no longer used
            $table->dropColumn(['cid', 'sid']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Re-add cid and sid columns for rollback
            $table->unsignedBigInteger('cid')->nullable()->after('is_admin');
            $table->unsignedBigInteger('sid')->nullable()->after('cid');

            // Re-add indexes for rollback
            $table->index('cid');
            $table->index('sid');
        });
    }
};
