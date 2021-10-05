<?php

namespace Osoobe\Utilities\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Osoobe\Utilities\Http\Resources\BootstrapTableCollection;
use Osoobe\Utilities\Http\Resources\Select2Collection;
use Osoobe\Utilities\Http\Resources\Select2Resource;
use Osoobe\Utilities\Traits\ResourceControllerTrait;

class AjaxController extends Controller {

    use ResourceControllerTrait;

    public function __construct()
    {
        $route = request()->route();
        if ( !empty($route) && $route->getName() == 'api.resource.get') {
            $slug = request()->route()->parameters['slug'];
            $configs = config("api-endpoints.$slug");
            if ( $configs && !empty($configs['middleware'])) {
                $this->middleware($configs['middleware']);
            }
        }
    }



}
