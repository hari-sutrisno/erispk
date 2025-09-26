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
            if (!Schema::hasColumn('users', 'photo')) {
                $table->string('photo')->nullable()->after('email');
                if (!Schema::hasColumn('users', 'extra_attributes')) {
                    $table->json('extra_attributes')->nullable()->after('photo');
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'photo')) {
                $table->dropColumn('photo');
            }
            if (Schema::hasColumn('users', 'extra_attributes')) {
                $table->dropColumn('extra_attributes');
            }
        });
    }
};
