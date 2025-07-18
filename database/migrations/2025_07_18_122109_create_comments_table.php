<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('article_id');
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('content');
            $table->uuid('parent_id')->nullable();
            $table->enum('status', ['published', 'hidden', 'deleted'])->default('published');

            $table->unsignedInteger('likes_count')->default(0);
            $table->unsignedInteger('reports_count')->default(0);
            $table->unsignedInteger('upVotes_count')->default(0);
            $table->unsignedInteger('downVotes_count')->default(0);

            $table->timestamps();

            $table->index(['article_id', 'parent_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
