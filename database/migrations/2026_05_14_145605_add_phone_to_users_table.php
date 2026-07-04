<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * NOTE: phone column already added in add_phone_address_to_users_table migration.
     * This migration is intentionally a no-op to preserve migration history.
     */
    public function up(): void
    {
        // phone already exists — no action needed
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // no action needed
    }
};
