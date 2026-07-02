<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Same root cause as 2026_07_02_000000_fix_s_galleries_missing_defaults:
 * sGalleryController::setTranslate() calls sGalleryField::where(...)->firstOrCreate()
 * with no attributes, which inserts a blank row before filling it in. s_gallery_fields.key
 * has no default, so that blank insert fails with "Field 'key' doesn't have a default value" —
 * reproduced when adding alt/title/description text to a gallery image in the manager.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('s_gallery_fields', function (Blueprint $table) {
            $table->integer('key')->default(0)->change();
        });
    }

    public function down(): void
    {
        Schema::table('s_gallery_fields', function (Blueprint $table) {
            $table->integer('key')->default(null)->change();
        });
    }
};
