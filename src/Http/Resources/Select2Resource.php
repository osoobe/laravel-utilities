<?php

namespace Osoobe\Utilities\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Osoobe\Utilities\Helpers\Utilities;

class Select2Resource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request) {
        $configs = $request->input('model_configs');
        $data = $this->getAttributes();
        if ( !empty($configs) ) {
            foreach($configs['includes'] as $key) {
                $data[$key] = $this->$key;
            }
            $id = $configs['id_column'];
            $text = $configs['text_column'];
            $data['id'] = (int) $this->$id;
            $data['text'] = $this->$text;
        } else {
            if ( empty($data['text']) ) {
                $data['text'] = Utilities::getObjectValue($this, ['text', 'name', 'title', 'seo_title'], '');
            }
        }
        $data['model_id'] = (int) $this->id;
        $data['id'] = (int) $this->id;
        return $data;
    }

}
