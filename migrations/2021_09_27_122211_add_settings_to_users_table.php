<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class AddSettingsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $table = config('user-setting.table');
        Schema::table($table, function (Blueprint $table) {
            $table->text('settings')->nullable()->comment('user setting');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $table = config('user-setting.table');
        Schema::table($table, function (Blueprint $table) {
            $table->dropColumn('settings');
        });
    }
}
