<?php

use Illuminate\Support\Facades\DB;

return [
    'avatar_column' => 'avatar_url',
    'disk' => env('FILESYSTEM_DISK', 'public'),
    'visibility' => 'public', // or replace by filesystem disk visibility with fallback value
    'show_custom_fields' => true,
    'custom_fields' => [
        'serial_number' => [
            'type' => 'text', // required
            'label' => 'No. Correlativo', // required
            'placeholder' => 'Ingrese el Número de Guía a imprimir', // optional
            'id' => 'serial_number', // optional
            'required' => true, // optional
            'rules' => [], // optional
            'hint_icon' => '', // optional
            'hint' => '', // optional
            'suffix_icon' => '', // optional
            'prefix_icon' => '', // optional
            'default' => '', // optional
            'column_span' => 'full', // optional
            'autocomplete' => false, // optional
        ],
        'departament_id' => [
            'type' => 'select', // required
            'label' => 'Departamento Origen', // required
            'placeholder' => 'Seleccione el departamente de origen del usuario', // optional
            'id' => 'departament_id', // optional
            'required' => true, // optional
            'options' => [], // optional
            'selectable_placeholder' => true, // optional
            'native' => true, // optional
            'preload' => true, // optional
            'suffix_icon' => '', // optional
            'default' => '', // optional
            'searchable' => false, // optional
            'column_span' => 'full', // optional
            'rules' => [], // optional
            'hint_icon' => '', // optional
            'hint' => '', // optional
        ],
    ]
];
