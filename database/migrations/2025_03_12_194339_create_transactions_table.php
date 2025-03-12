<?php

use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Account::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Category::class)->constrained()->cascadeOnDelete();
            $table->string('type');
            $table->foreignIdFor(Account::class, 'transfer_from')
                ->nullable()
                ->constrained('accounts')
                ->cascadeOnDelete();
            $table->foreignIdFor(Account::class, 'transfer_to')
                ->nullable()
                ->constrained('accounts')
                ->cascadeOnDelete();
            $table->float('amount');
            $table->text('payee');
            $table->date('date');
            $table->text('notes')->nullable();
            $table->json('attachments')->nullable();
            $table->boolean('status')->default(false);
            $table->boolean('is_recurring')->default(false);
            $table->string('frequency')->nullable();
            $table->date('recurring_end')->nullable();
            $table->foreignIdFor(Transaction::class, 'parent_id')
                ->nullable()
                ->constrained('transactions')
                ->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
