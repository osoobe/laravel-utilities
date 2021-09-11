<?php

namespace Osoobe\Utilities\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Select2Resource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request) {
        $configs = $request->input('select2_configs');
        foreach($configs['includes'] as $key) {
            $data[$key] = $this->$key;
        }
        $id = $configs['id_column'];
        $text = $configs['text_column'];
        if ( isset($this->id) ) {
            $data['model_id'] = $this->id;
        }
        $data['id'] = $this->$id;
        $data['text'] = $this->$text;
        return $data;
    }
}
