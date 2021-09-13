<?php

namespace Osoobe\Utilities\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Osoobe\Utilities\Http\Resources\BootstrapTableCollection;
use Osoobe\Utilities\Http\Resources\Select2Collection;
use Osoobe\Utilities\Http\Resources\Select2Resource;

class AjaxController extends Controller {

    public function getResource(Request $request, $slug, $format='bst') {
        $configs = config("api-endpoints.$slug");
        if ( !$configs ) {
            return response()->json([
                'result' => []
            ]);
        }
        $class_name = $configs['model'];
        $query = $class_name::where($configs['id_column'], '!=', null);
        $term = $request->query('term');
        if ( !empty($term) ) {
            $query->where($configs["text_column"], "like", "%$term%");
        }
        if ( !empty($configs['conditions']) ) {
            $query->where($configs['conditions']);
        }
        $request->merge(['model_configs' => $configs]);
        switch ( $format ) {
            case 'select2':
                return new Select2Collection(Select2Resource::collection($query->get()));
            default:
                if ( !empty($configs['resource']) ) {
                    return new BootstrapTableCollection($configs['resource']::collection($query->get()));
                }
                return new BootstrapTableCollection($query->get());
        }

    }

}
