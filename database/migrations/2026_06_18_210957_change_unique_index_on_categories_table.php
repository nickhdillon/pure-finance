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
        Schema::table('categories', function (Blueprint $table) {
            $table->dropUnique('categories_name_user_id_unique');

            $table->unique(
                ['user_id', 'parent_id', 'name'],
                'categories_user_parent_name_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropUnique('categories_user_parent_name_unique');

            $table->unique(['name', 'user_id']);
        });
    }
};
