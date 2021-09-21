<?php

namespace  Osoobe\Utilities\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MigrationHelper {


    /**
     * Add Full Text Search to the given table.
     *
     * This will create a new index for the Full Text Search column.
     * To drop support for Full Text Search, you the `$table->dropIndex($index)`;
     *
     * @param string $table     Table name
     * @param array $columns    List of columns for create the composite index.
     * @param string $index     Index name
     * @return void
     */
    public static function addFullTextSearch(string $table, array $columns, string $index="search") {
        $col_text = implode(',', $columns);
        // try {
            DB::statement("ALTER TABLE $table ADD FULLTEXT `$index` ($col_text)");
            DB::statement("ALTER TABLE $table ENGINE = MyISAM");
        // } catch (\Throwable $th) {
        //     Log::debug($th->getMessage());
        // }
    }
}



?>
