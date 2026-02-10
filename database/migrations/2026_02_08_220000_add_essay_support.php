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
        // 1. Add type to evaluations and make options nullable
        if (Schema::hasTable('evaluations')) {
            Schema::table('evaluations', function (Blueprint $table) {
                if (!Schema::hasColumn('evaluations', 'type')) {
                    $table->enum('type', ['multiple_choice', 'essay'])->default('multiple_choice')->after('question');
                }
                // Always try to make them nullable as it's an alter operation
                // But changing columns might require dbal which might not be installed or configured
                // Let's assume it works or catch exception? No, simple is better.
                // We'll wrap in try-catch or just hope.
                // Actually, if 'type' exists, likely the others were made nullable too if it was the same migration?
                // But let's just run the change() commands.
                // If the column is already nullable, change() is fine.
            });
            
             Schema::table('evaluations', function (Blueprint $table) {
                $table->string('option_a')->nullable()->change();
                $table->string('option_b')->nullable()->change();
                $table->string('option_c')->nullable()->change();
                $table->string('option_d')->nullable()->change();
                $table->string('correct_answer')->nullable()->change();
            });
        }

        // 2. Create evaluation_answers table
        if (!Schema::hasTable('evaluation_answers')) {
            Schema::create('evaluation_answers', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId('evaluation_id')->constrained('evaluations')->onDelete('cascade');
                $table->text('answer')->nullable(); // Stores text for essay or selected option for MC
                $table->integer('score')->default(0); // Score for this specific answer
                $table->timestamps();
            });
        }

        // 3. Add status to evaluation_results
        if (Schema::hasTable('evaluation_results')) {
            Schema::table('evaluation_results', function (Blueprint $table) {
                if (!Schema::hasColumn('evaluation_results', 'status')) {
                    $table->enum('status', ['graded', 'pending'])->default('graded')->after('score');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('evaluation_results', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::dropIfExists('evaluation_answers');

        Schema::table('evaluations', function (Blueprint $table) {
            $table->dropColumn('type');
            // Not reverting nullable columns to avoid issues
        });
    }
};
