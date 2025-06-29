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
        Schema::table('bills', function (Blueprint $table) {
            $table->boolean('first_alert_sent')->after('first_alert_time')->default(false);
            $table->boolean('second_alert_sent')->after('second_alert_time')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bills', function (Blueprint $table) {
            $table->dropColumn('first_alert_sent');
            $table->dropColumn('second_alert_sent');
        });
    }
};
