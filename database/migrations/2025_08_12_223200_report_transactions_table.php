<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Category;
use App\Models\Account;
use App\Models\Report;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('report_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Report::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Account::class)->nullable()->constrained()->nullOnDelete();
            $table->foreignIdFor(Category::class)->nullable()->constrained()->nullOnDelete();
            $table->string('type');
            $table->string('payee');
            $table->float('amount');
            $table->date('date');
            $table->json('snapshot');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_transactions');
    }
};
