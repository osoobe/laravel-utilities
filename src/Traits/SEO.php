<?php

namespace  Osoobe\Utilities\Traits;


trait SEO {

    public abstract function getRouteURL();
    public abstract function getSEOTitleAttribute();
    public abstract function getSEODescriptionAttribute();

    public function getURLAttribute(){
        return $this->getRouteURL();
    }


}


?>
