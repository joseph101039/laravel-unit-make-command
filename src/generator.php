<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Controller Path
    |--------------------------------------------------------------------------
    |
    | This option determines where all the controller class files will be
    | put for your application. Typically, this is a fixed value within the
    | storage directory. However, as usual, you are free to change this value.
    |
    | This option determines class name of the class files will be put for your
    | application under controller_path. You are free to change this value.
    |
    | The elements in classes array with replace the token element's key + Class
    | with the element's value in stub file.
    */

    'controller' => [
        'enable' => true,
        'subnamespace' => 'Http\Controllers',
        'subfolder' => '',
        'classes' => [
            'controller' =>  'Controller',
            'transformer' => 'Transformer',
            'request' => 'Form'
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Entity Path, Target Class File to be created under Controller Default Path
    |--------------------------------------------------------------------------
    |
    | This option determines where all the controller class files will be
    | put for your application. Typically, this is a fixed value within the
    | storage directory. However, as usual, you are free to change this value.
    |
    | This option determines class name of the class files will be put for your
    | application under controller_path. You are free to change this value.

    */
    'entity' => [
        'enable' => true,
        'subnamespace' => 'Management',
        'subfolder' => '',
        'classes' => [
            'service' => 'Service',
            'repository' => 'Repository',
            'entity' => 'Entity'
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Create SearchService
    |--------------------------------------------------------------------------
    |
    | This option determines whether to create SearchService folder under
    | entity_path. Search class and IFilter folder will be created under SearchService.
    | You are free to change this value.
    |
    */


    'search' => [
        'enable' => true,
        'subnamespace' => 'Management',
        'subfolder' => 'SearchService',
        'classes' => ['search' => 'Search'],
        'create_folder' => 'Filters',       // create addition folder under subfolder.
    ],





    /*
    |--------------------------------------------------------------------------
    | Create resource routes
    |--------------------------------------------------------------------------
    |
    | This option determines whether to create CRUD resource routes in api.php
    | You are free to change this value.
    |
    */

    'route' => [
        'enable' => true,
        'file' => base_path('routes/api.php'),
        'method' => ['get', 'post', 'put', 'delete'],       // http methods
        'allow_duplicate' => false,
    ],




    'stub' => [
        'path' => resource_path('stubs'),       // all the ClassName.stub are put under resources/stubs/
        'extension' => '.stub'
    ],


];
