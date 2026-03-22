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
            $table->string('role')->default('user')->after('password'); // user, admin
            $table->boolean('is_admin')->default(false)->after('role');
            $table->unsignedBigInteger('cid')->nullable()->after('is_admin'); // company_id
            $table->unsignedBigInteger('sid')->nullable()->after('cid'); // site_id
            $table->unsignedBigInteger('authorized_by')->nullable()->after('sid'); // which admin authorized this user

            // Add indexes for performance
            $table->index('role');
            $table->index('is_admin');
            $table->index('cid');
            $table->index('sid');
            $table->index('authorized_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role']);
            $table->dropIndex(['is_admin']);
            $table->dropIndex(['cid']);
            $table->dropIndex(['sid']);
            $table->dropIndex(['authorized_by']);

            $table->dropColumn(['role', 'is_admin', 'cid', 'sid', 'authorized_by']);
        });
    }
};
