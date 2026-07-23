<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * ⚡ PERFORMANCE: Add composite index for QapersonId and updated_at
     * This significantly speeds up the /qa-section route query
     */
    public function up(): void
    {
        Schema::table('avatar_leads', function (Blueprint $table) {
            // Composite index for the common query pattern: WHERE QapersonId = ? ORDER BY updated_at DESC
            $table->index(['QapersonId', 'updated_at'], 'idx_qa_person_updated');
            
            // Individual index on QapersonId for faster filtering
            if (!$this->indexExists('avatar_leads', 'avatar_leads_qapersonid_index')) {
                $table->index('QapersonId', 'avatar_leads_qapersonid_index');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('avatar_leads', function (Blueprint $table) {
            $table->dropIndex('idx_qa_person_updated');
            if ($this->indexExists('avatar_leads', 'avatar_leads_qapersonid_index')) {
                $table->dropIndex('avatar_leads_qapersonid_index');
            }
        });
    }

    /**
     * Check if an index exists
     */
    private function indexExists($table, $indexName): bool
    {
        $connection = Schema::getConnection();
        $databaseName = $connection->getDatabaseName();
        
        $result = $connection->select(
            "SELECT COUNT(*) as count 
             FROM information_schema.statistics 
             WHERE table_schema = ? 
             AND table_name = ? 
             AND index_name = ?",
            [$databaseName, $table, $indexName]
        );
        
        return $result[0]->count > 0;
    }
};

