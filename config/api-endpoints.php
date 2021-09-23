<?php

return [
    //
    'user' => [
        'model' => "App\Models\User",
        'id_column' => 'id',
        'text_column' => 'name',
        'full_text_search' => [],
        'includes' => [
            "id",
            "name",
            'slug'
        ],
        'conditions' => [],
        // 'middleware' => ['auth:sanctum', 'verified']
    ],
]

?>
