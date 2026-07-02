<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_content', function (Blueprint $table) {
            $table->unsignedInteger('hits')->default(0)->after('cacheable');
        });
    }

    public function down(): void
    {
        Schema::table('site_content', function (Blueprint $table) {
            $table->dropColumn('hits');
        });
    }
};
