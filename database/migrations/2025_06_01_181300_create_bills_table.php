<?php

use App\Models\User;
use App\Models\Category;
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
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->foreignIdFor(Category::class)->constrained()->cascadeOnDelete();
            $table->float('amount');
            $table->date('date');
            $table->string('frequency')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('paid')->default(false);
            $table->json('attachments')->nullable();
            $table->string('first_alert')->nullable();
            $table->string('first_alert_time')->nullable();
            $table->string('second_alert')->nullable();
            $table->string('second_alert_time')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bills');
    }
};
