<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->text('google_maps_embed')->nullable()->after('state');
            $table->string('google_maps_link')->nullable()->after('google_maps_embed');
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn(['google_maps_embed', 'google_maps_link']);
        });
    }
};
