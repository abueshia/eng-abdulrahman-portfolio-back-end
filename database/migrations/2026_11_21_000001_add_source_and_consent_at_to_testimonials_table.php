<?php

/**
 * Add Source and Consent At to Testimonials Table Migration
 *
 * Adds 'source' (dashboard vs website) and 'consent_at' timestamp
 * to track public website submissions and privacy compliance.
 */

declare(strict_types=1);

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
        Schema::table('testimonials', static function (Blueprint $table): void {
            $table->string('source', 20)->default('dashboard')->after('sort_order')->index();
            $table->timestamp('consent_at')->nullable()->after('source');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('testimonials', static function (Blueprint $table): void {
            $table->dropColumn(['source', 'consent_at']);
        });
    }
};
