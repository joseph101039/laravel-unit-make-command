<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Classes Path
    |--------------------------------------------------------------------------
    |
    | 以下三個建立類型 :  'controller', 'entity', 'search'
    | (1) 'enable': 只有為 true 時才會建立文件
    | (2) 'subnamespace', 'subfolder', 'classes': 在 app/subnamespace/subfolder 底下建立 classes 指定的文件
    | 以 search 類型為例，輸入 php artisan make:unit Folder/User 會在
    | app/Management/Folder/SearchService/User 底下建立 SearchService.php
    | (3) create_folder: optional, 指定建立額外空資料夾
    |
    | This option determines where all the class files will be
    | put for your application. Typically, this is a fixed value within the
    | storage directory. However, as usual, you are free to change this value.
    |
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
    | Entity Path, Target Class File to be created under Entity Path
    |--------------------------------------------------------------------------
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
    | Search Path, Target Class File to be created under Search Path
    |--------------------------------------------------------------------------
    */

    'search' => [
        'enable' => true,
        'subnamespace' => 'Management',
        'subfolder' => 'SearchService',
        'classes' => ['search' => 'Search'],
        'create_folder' => 'Filters',
    ],


    /*
    |--------------------------------------------------------------------------
    | Stub Path
    |--------------------------------------------------------------------------
    |
    | (1) 'path': 指定 stub 文件在專案中位置
    | (2) 'extension': 指定 stub 文件副檔名，程式撈取檔案名稱為 類型.stub, 如 Search.stub, 或是 Route.post.stub
    |
    */

    'stub' => [
        'path' => resource_path('stubs'),       // all the ClassName.stub are put under resources/stubs/
        'extension' => '.stub'
    ],



    /*
    |--------------------------------------------------------------------------
    | Resource routes
    |--------------------------------------------------------------------------
    |
    | (1) 'enable': 只有為 true 時才會建立路由
    | (2) 'file': 指定新增路由的文件
    | (3) method: 指定需要新增的 http method 種類
    | (4) allow_duplicate: 是否需要避免建立重複 URI 的路由
    */

    'route' => [
        'enable' => true,
        'file' => base_path('routes/api.php'),
        'method' => ['get', 'post', 'put', 'delete'],       // http methods
        'allow_duplicate' => false,
    ],






];
