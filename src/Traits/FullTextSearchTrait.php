<?php

namespace Osoobe\Utilities\Traits;

trait FullTextSearchTrait {

    public function scopeFullTextSearch($query, array $columns, string $text, bool $boolMode=false) {
        return $query->whereRaw(
            self::buildFullTextSearch($columns, $text, $boolMode)
         );
    }

    public function scopeOrFullTextSearch($query, array $columns, string $text, bool $boolMode=false) {
        return $query->orWhereRaw(
            self::buildFullTextSearch($columns, $text, $boolMode)
         );
    }

    public static function buildFullTextSearch(array $columns, string $text, bool $boolMode=false) {
        $col_text = implode(',', $columns);
        $text = preg_replace('/[^a-z\d ]/i', ' ', $text);
        return "MATCH ($col_text) AGAINST ('$text' ".( ($boolMode)? "IN BOOLEAN MODE" : "" ).")";
    }

    public static function selectFTSScore(array $columns, string $text, bool $boolMode=false) {
        return static::buildFullTextSearch($columns, $text, $boolMode)." as fts_score";
    }

}

?>
