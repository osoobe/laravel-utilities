<?php

namespace Osoobe\Utilities\Http\Controllers;

use Illuminate\Http\Request;
use Osoobe\Utilities\Http\Controllers\Controller;
use Osoobe\Utilities\Http\Resources\Select2Collection;
use Osoobe\Utilities\Http\Resources\Select2Resource;

class AjaxController extends Controller {

    public function getSelect2Resource(Request $request, $slug) {
        $configs = config("api-endpoints.$slug");
        if ( !$configs ) {
            return response()->json([
                'result' => []
            ]);
        }
        $class_name = $configs['model'];
        $query = $class_name::where($configs['id_column'], '!=', null);
        if ( !empty($configs['conditions']) ) {
            $query->where($configs['conditions']);
        }
        $request->merge(['select2_configs' => $configs]);
        return new Select2Collection(Select2Resource::collection($query->get()));
    }

}
