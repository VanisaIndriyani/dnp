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
        Schema::table('evaluation_answers', function (Blueprint $table) {
            $table->foreignId('evaluation_result_id')->nullable()->after('user_id')->constrained('evaluation_results')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('evaluation_answers', function (Blueprint $table) {
            $table->dropForeign(['evaluation_result_id']);
            $table->dropColumn('evaluation_result_id');
        });
    }
};
