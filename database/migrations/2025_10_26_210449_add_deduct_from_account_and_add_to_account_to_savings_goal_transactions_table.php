<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Account;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('savings_goal_transactions', function (Blueprint $table) {
            $table->boolean('deduct_from_account')->default(false)->after('withdrawal_amount');
            $table->boolean('add_to_account')->default(false)->after('withdrawal_amount');
        });

        Schema::table('savings_goals', function (Blueprint $table) {
            $table->dropForeign(['account_id']);
            $table->foreignIdFor(Account::class)->change()->constrained()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('savings_goal_transactions', function (Blueprint $table) {
            $table->dropColumn(['deduct_from_account', 'add_to_account']);
        });
    }
};
