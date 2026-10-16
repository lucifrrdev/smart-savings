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
            $table->string('nisn')->nullable()->change();
            if (!Schema::hasColumn('users', 'nip')) {
                $table->string('nip', 30)->nullable()->unique()->after('nisn');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'nip')) {
                $table->dropUnique(['nip']);
                $table->dropColumn('nip');
            }
            $table->string('nisn')->nullable(false)->change();
        });
    }
};
