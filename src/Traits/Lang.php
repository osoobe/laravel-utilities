<?php

namespace  Osoobe\Utilities\Traits;

use DB;

trait Lang
{

    public function scopeLang($query)
    {
        return $query->where('lang', 'like', \App::getLocale());
    }

    public function scopeLangEN($query) {
        return $query->where('lang', 'en');
    }

    public function language()
    {
        return $this->hasOne('App\Language', 'iso_code', 'lang');
    }

    public function isLangRTL()
    {
        return $this->language()->first()->is_rtl;
    }

}
