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
        Schema::table('evaluations', function (Blueprint $table) {
            $table->string('type')->default('multiple_choice')->after('id'); // multiple_choice, essay, true_false
            $table->string('option_a')->nullable()->change();
            $table->string('option_b')->nullable()->change();
            $table->string('option_c')->nullable()->change();
            $table->string('option_d')->nullable()->change();
        });

        // Separate schema call for dropping column to avoid issues
        Schema::table('evaluations', function (Blueprint $table) {
            $table->dropColumn('correct_answer');
        });

        Schema::table('evaluations', function (Blueprint $table) {
            $table->string('correct_answer')->after('option_d');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('evaluations', function (Blueprint $table) {
            $table->dropColumn('type');
            $table->dropColumn('correct_answer');
        });

        Schema::table('evaluations', function (Blueprint $table) {
            $table->enum('correct_answer', ['a', 'b', 'c', 'd']);
            $table->string('option_a')->nullable(false)->change();
            $table->string('option_b')->nullable(false)->change();
            $table->string('option_c')->nullable(false)->change();
            $table->string('option_d')->nullable(false)->change();
        });
    }
};
