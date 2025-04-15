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
        Schema::table('accounts', function (Blueprint $table) {
            $table->string('slug')->after('name')->nullable();
        });

        Schema::table('planned_expenses', function (Blueprint $table) {
            $table->string('slug')->after('name')->nullable();
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->string('slug')->after('payee')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->dropColumn('slug');
        });

        Schema::table('planned_expenses', function (Blueprint $table) {
            $table->dropColumn('slug');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
