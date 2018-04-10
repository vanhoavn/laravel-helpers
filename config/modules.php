<?php
return [
    // root namespace of the modules
    'namespace'  => 'Modules',

    // root path of the module, relative to base_path
    'path'       => 'modules',

    // the base model class
    'base_model' => 'Illuminate\Database\Eloquent\Model',

    'stubs'         => [],

    //
    'sub_namespace' => [
        'default' => [
            'namespace' => 'Modules',
            'path'      => 'modules',
        ],
    ],
];