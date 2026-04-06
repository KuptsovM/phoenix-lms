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
        Schema::table('courses', function (Blueprint $table) {
            $table->string('title')->after('id');
            $table->string('slug')->unique()->after('title');
            $table->text('description')->nullable()->after('slug');
            $table->string('status')->default('draft')->index()->after('description');
            $table->boolean('is_featured')->default(false)->after('status');
            $table->timestamp('published_at')->nullable()->after('is_featured');
            $table->foreignId('author_id')->nullable()->after('published_at')->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropForeign(['author_id']);
            $table->dropUnique(['slug']);
            $table->dropIndex(['status']);
            $table->dropColumn([
                'title',
                'slug',
                'description',
                'status',
                'is_featured',
                'published_at',
                'author_id',
            ]);
        });
    }
};
