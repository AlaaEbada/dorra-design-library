<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('designs', function (Blueprint $table) {
            $table->index(['category_id', 'status', 'created_at', 'id'], 'designs_category_status_created_id_index');
            $table->index('title', 'designs_title_index');
        });
    }

    public function down(): void
    {
        if (! Schema::hasIndex('designs', 'designs_category_id_foreign')) {
            Schema::table('designs', function (Blueprint $table) {
                $table->index('category_id', 'designs_category_id_foreign');
            });
        }

        Schema::table('designs', function (Blueprint $table) {
            $table->dropIndex('designs_title_index');
            $table->dropIndex('designs_category_status_created_id_index');
        });
    }
};
