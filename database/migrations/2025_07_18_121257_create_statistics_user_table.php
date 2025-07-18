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
        Schema::create('statistics_user', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->constrained();
            $table->integer('total_comments_created')->default(0);
            $table->integer('total_upVote_comments')->default(0);
            $table->integer('total_downVote_comments')->default(0);
            $table->integer('total_liked_comments')->default(0);
            $table->integer('total_report_times')->default(0);
            $table->integer('total_comments')->default(0);
            $table->integer('total_muted_times')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('statistics_user');
    }
};
