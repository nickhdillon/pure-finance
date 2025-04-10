<?php

declare(strict_types=1);

use App\Models\Category;
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
        Schema::create('planned_expenses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignIdFor(Category::class)->constrained();
            $table->float('monthly_amount');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('planned_expenses');
    }
};
