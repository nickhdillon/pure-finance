<?php

declare(strict_types=1);

use App\Models\Transaction;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('incomes', function (Blueprint $table): void {
            $table->foreignIdFor(Transaction::class)
                ->nullable()
                ->after('user_id')
                ->constrained()
                ->nullOnDelete();
            $table->boolean('received')->default(false)->after('notes');
        });
    }

    public function down(): void
    {
        Schema::table('incomes', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('transaction_id');
            $table->dropColumn('received');
        });
    }
};
