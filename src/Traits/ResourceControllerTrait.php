<?php

namespace  Osoobe\Utilities\Traits;

use Illuminate\Http\Request;
use Osoobe\Utilities\Http\Resources\BootstrapTableCollection;
use Osoobe\Utilities\Http\Resources\Select2Collection;
use Osoobe\Utilities\Http\Resources\Select2Resource;

trait ResourceControllerTrait {

    public function getResource(Request $request, $slug, $format='bst') {
        $configs = config("api-endpoints.$slug");
        if ( !$configs ) {
            return response()->json([
                'result' => []
            ]);
        }
        $class_name = $configs['model'];
        $query = $class_name::where($configs['id_column'], '!=', null);

        if ( !empty($configs['helper']) ) {
            $query = $configs['helper']($request, $query, $configs);
        }

        $request->merge(['model_configs' => $configs]);
        switch ( $format ) {
            case 'select2':
                return $this->select2Resposne($request, $query, $configs);
            default:
                return $this->bootstrapTableResponse($request, $query, $configs);
        }
    }

    protected function select2Resposne($request, $query, $configs) {
        $is_pagination = $request->input('paginate', false);
        $limit = $request->input('limit', ( $is_pagination )? 50: null );
        $sort = $request->input('sort', 'id');
        $order = $request->input('order', 'asc');
        $term = $request->query('term');

        $this->filterQuery($query, $term, $configs);
        $query->orderBy($sort, $order);
        return new Select2Collection(Select2Resource::collection($query->get()));
    }

    protected function bootstrapTableResponse($request, $query, $configs) {

        $offset = $request->input('offset', null);
        $limit = $request->input('limit', ( $offset == null )? 50: null );
        $sort = $request->input('sort', 'id');
        $order = $request->input('order', 'asc');
        $term = $request->query('search');

        $this->filterQuery($query, $term, $configs);
        $query->orderBy($sort, $order);
        if ( $offset != null  ) {
            $query = $query->offset($offset);
        }
        $data = ( $limit == null && $offset == null)? $query->get() : $query->paginate($limit);
        if ( !empty($configs['resource']) ) {
            return new BootstrapTableCollection($configs['resource']::collection( $data ));
        }
        return new BootstrapTableCollection( $data );
    }

    protected function filterQuery($query, $term, $configs) {

        if ( !empty($term) ) {
            if ( !empty($configs["full_text_search"]) ) {
                $query->FullTextSearch($configs["full_text_search"], $term);
            } else {
                $query->where($configs["text_column"], "like", "%$term%");
            }
        }
        if ( !empty($configs['conditions']) ) {
            $query->where($configs['conditions']);
        }
    }
}
