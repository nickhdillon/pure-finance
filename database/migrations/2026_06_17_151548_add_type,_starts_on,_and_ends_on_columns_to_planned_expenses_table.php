<?php

use App\Enums\PlannedExpenseType;
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
        Schema::table('planned_expenses', function (Blueprint $table) {
            $table->string('type')->after('monthly_amount')->default(PlannedExpenseType::RECURRING->value);
            $table->date('starts_on')->after('type')->nullable();
            $table->date('ends_on')->after('starts_on')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('planned_expenses', function (Blueprint $table) {
            $table->dropColumn(['type', 'starts_on', 'ends_on']);
        });
    }
};
