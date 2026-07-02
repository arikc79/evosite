<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * seiger/sgallery's upload controllers call firstOrCreate() with no
 * attributes, which inserts a blank row before filling it in via a
 * follow-up update(). s_galleries.parent and .file have no default,
 * so that blank insert fails with "Field 'parent' doesn't have a
 * default value". Giving them defaults lets the vendor flow work as
 * intended without patching vendor code.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('s_galleries', function (Blueprint $table) {
            $table->integer('parent')->default(0)->change();
            $table->string('file', 256)->default('')->change();
        });
    }

    public function down(): void
    {
        Schema::table('s_galleries', function (Blueprint $table) {
            $table->integer('parent')->default(null)->change();
            $table->string('file', 256)->default(null)->change();
        });
    }
};
