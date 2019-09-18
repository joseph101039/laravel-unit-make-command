<?php

namespace App\Console\Commands;

// refer to the key of config/generator.php
define('CONTROLLER_TYPE', 'controller');
define('ENTITY_TYPE', 'entity');
define('SEARCH_TYPE', 'search');
define ('ROUTE_TYPE', 'route');


use Illuminate\Console\Command;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Route;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

/* 測試
 run command:
 *  php artisan make:unit TestMake
 *
 * */
//ref: https://learnku.com/laravel/t/12500/command-line-combat-hand-in-hand-to-create-a-laravel-crud-code-generator-for-you
class UnitMakeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:unit {name} {--overwrite}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Controller, Service, Repository and Form class base on the given model name which match the route create in api.php';


//    /**
//     * The route collection instance.
//     *
//     * @var \Illuminate\Routing\RouteCollection
//     */
//    protected $routes;

    protected $type = 'Unit';

    /**
     * Get the stub file for the generator.
     *
     * @param string $class
     * @param string $method
     * @return string
     */
    public function getStub($class, $method = null)
    {
        if($class === 'Route' and is_string($method)) {
            return file_get_contents(config('generator.stub.path') . '/' . $class. '.' .$method . config('generator.stub.extension'));
        }
        return file_get_contents(config('generator.stub.path') . '/' . $class . config('generator.stub.extension'));
    }


    public function handle()
    {
        $name = $this->getNameInput();

        $this->makeClass(CONTROLLER_TYPE, $name);
        $this->makeClass(ENTITY_TYPE, $name);
        $this->makeClass(SEARCH_TYPE, $name);
        $this->makeRoute(ROUTE_TYPE, $name);

    }

    public function makeClass($type, $name)
    {
        // 當 config enable 不存在 為 null, 空字串, 0, false 時不執行
        if(empty(config("generator.{$type}.enable"))) {
            return;
        }

        if ($classes = config("generator.{$type}.classes")) {
            foreach ($classes as $class) {
                if (($namespace = $this->getClassNamespace($type)) &&
                    $path = $this->getPath($type)) {

                    $stub = $this->getStub($class);
                    $stub = $this->replaceNamespace($stub, $name)->replaceAdditionalToken($stub)->replaceClass($stub);

                    if (!file_exists($path)) {
                        mkdir($path, 0777, true);
                    }

                    if (!$this->makeAdditionalDirectory($type, $path)) {
                        $this->error("在 {$path} 下建立子資料夾失敗");
                    }

                    $file_path = $path.'\\'.$class.".php";
                    $file_name = $namespace.'\\'.$class.'.php';

                    if (!file_exists($file_path)) {
                        file_put_contents($file_path, $stub);
                        print("{$file_name} 已成功建立。\n");
                    } else {
                        $this->error("{$file_name} 檔案已經存在，略過建立。");
                    }
                }
            }
        }

    }

    public function makeRoute($type, $name)
    {
        if(empty(config("generator.{$type}.enable"))) {
            print("Routes are not created.\n");
            return;
        }

        if(($api_file = config("generator.{$type}.file")) &&
            !empty($methods = config("generator.{$type}.method"))
        ) {
            $methods = $this->getNonDuplicateMethods($name, $methods);
            $this->prependRoutes($api_file, $name, $methods);
        }
    }

    /**
     *  找到 api..php  prepend routes file
     * */

    private function prependRoutes($file, $name, $methods)
    {
        if (!file_exists($file)) {
            $this->error('Error: '.$file. " is not found\n");
            return false;
        }

        file_put_contents($file, "// TODO: 檢查 {$name} 建立 Routes URI 是否正確\n",  FILE_APPEND);
        foreach($methods as $method) {
            $stub = $this->getStub('Route', $method);

            // replace the string
            if($stub !== false) {

                $url = $this->generateUri($name, $method);
                $stub = str_replace('{{Uri}}', $url, $stub);
                $name = str_replace('/', '\\', $name);
                $stub = str_replace('{{Name}}', $name, $stub);
                $result = file_put_contents($file, $stub,  FILE_APPEND);

                if($result === false) {
                    $upper_method = strtoupper($method);
                    $this->error("Error: Create {$upper_method} - {$url} route failed\n");
                } else {
                    print("{$method} - {$url} 已經成功建立\n");

                }
            } else {
                $this->error("Error: Route.{$method}.stub is not found\n");
            }
        }
    }

    /**
     *  在 routes/api.php 中 api/uri + http method 不可以重複,  需要過濾避免新增的 route ，若是 URL 重複，可能會覆蓋掉上面已經定義過的 route
     *
     * */
    public function getNonDuplicateMethods($name, $methods)
    {
        // check if duplicate routes is allowed
        $allow_duplicate = config("generator.".ROUTE_TYPE.".allow_duplicate");

        if($allow_duplicate) {
            return $methods;
        }

        // get current routes
        $routes = $this->getRoutes();

        // check if route is already existed
        $target_methods = [];

        foreach($methods as $method) {
            $out_url = 'api/'. $this->generateUri($name, $method);
            if(!$this->isDuplicateRoute($routes, $out_url, $method)) {
                $target_methods[] = $method;
            }
        }

        return $target_methods;
    }

    private function isDuplicateRoute($routes, $url, $method)
    {
        //  URL 中遇到 \{...} 或是 /{...} 換成 /{*}，若是直接用  preg_replace 只會替換最外層，將 \{aa}/{bb} 換成 /{*}
        $search_url = $this->replaceBraceContent($url);
        $method = strtoupper($method);
        foreach($routes as $route) {
            if(($search_url === $route['format'])  &&
                in_array(strtoupper($method), $route['methods'])) {
                $this->error("{$method} - {$url} 路由已經存在，略過建立。");
                return true;
            }
        }
        return false;
    }


    private function generateUri($name, $method)
    {
        $unit_name = $this->getUnitName($name);
        $uri = str_singular(snake_case($unit_name));

        switch(strtoupper($method))
        {
            case "GET":
            case "HEAD":
                $url = $uri;
                break;
            case "POST":
                $url = $uri;
                break;
            case "PUT":
            case "PATCH":
                $url = $uri."/{{$uri}}";
                break;
            case "DELETE":
                $url = $uri."/{{$uri}}";
                break;
            default:
                $url = $uri;
        }
        return $url;
    }

    private function getRoutes()
    {
        $routes = \Route::getRoutes()->getRoutes();

        return collect($routes)->map(function($item) {
            $item =  collect($item);
            $data = $item->only(['uri', 'methods']);
//            $data->put('controller',  $item['action']['controller'] ?? null);
            $data->put('format',  $this->replaceBraceContent($data['uri']));
            return $data;
        });
    }

    protected function replaceBraceContent($target)
    {
        while($this->matchBrace($target)) {
            $target = $this->replaceBrace($target);
            $target = preg_replace('/[\\/\\\\]<.+>/', '/(*)', $target);
        }
        return $target;
    }

    private function replaceBrace($target)
    {
        $target = preg_replace('/{/', '<', $target, 1);
        return preg_replace( '/}/', '>', $target, 1);
    }

    private function matchBrace($target)
    {
        return preg_match('/{/', $target);
    }

    public function replaceClass($stub)
    {
        $all_classes = array_merge(config("generator.".CONTROLLER_TYPE.".classes"), config("generator.".ENTITY_TYPE.".classes"), config("generator.".SEARCH_TYPE.".classes"));
        foreach ($all_classes as $alias => $class) {
            $stub = str_replace('{{' . studly_case($alias) . 'Class}}', $class, $stub);
        }
        return $stub;
    }

    public function replaceNamespace(&$stub, $name)
    {
        $search = [
            '{{ControllerDefaultNamespace}}',
            '{{EntityDefaultNamespace}}',
            '{{SearchDefaultNamespace}}',
            '{{ControllerNamespace}}',
            '{{EntityNamespace}}',
            '{{SearchNamespace}}',
            '{{UnitName}}',
            '{{CamelUnitName}}',
            '{{PluralSnakeUnitName}}',
        ];

        $unit_name = $this->getUnitName($name);
        $replace = [
            $this->rootNamespace(CONTROLLER_TYPE),
            $this->rootNamespace(ENTITY_TYPE),
            $this->rootNamespace(SEARCH_TYPE),
            $this->getClassNamespace(CONTROLLER_TYPE),
            $this->getClassNamespace(ENTITY_TYPE),
            $this->getClassNamespace(SEARCH_TYPE),
            studly_case($unit_name),
            camel_case($unit_name),
            str_plural(snake_case($unit_name)),
        ];
        $stub = str_replace($search, $replace, $stub);
        return $this;
    }

    public function replaceAdditionalToken(&$stub)
    {
        $search = [
            '{{FilterFolder}}',
        ];

        $replace = [
            config('generator.search.create_folder'),
        ];

        $stub = str_replace($search, $replace, $stub);
        return $this;
    }

    /**
     * Get the destination class path.
     *
     * @param  string  $name
     * @return string
     */
    protected function getPath($type)
    {
        $name = Str::replaceFirst($this->rootNamespace($type), '', $this->getNameInput());
        $subfolder = $this->getSubfolder($type);
        return $this->laravel['path'].'/'.config("generator.{$type}.subnamespace").'/'.str_replace('\\', '/', $name).$subfolder;
    }

    /**
     * Get the desired class name from the input.
     *
     * @return string
     */
    protected function getNameInput()
    {
        // remove slash, backslash and whitespace from head and tail.

        return trim($this->argument('name'), '\\/ ');
    }

    /**
     * Get the root namespace for the class.
     *
     * @return string
     */
    protected function rootNamespace($type)
    {
//        print("type: ".$this->laravel->getNamespace() . config("generator.{$type}.subnamespace")."\n");
        if($type === CONTROLLER_TYPE || $type === ENTITY_TYPE || $type === SEARCH_TYPE) {
            return $this->laravel->getNamespace() . config("generator.{$type}.subnamespace");
        } else {
            $this->error('Error: Unknown type was given to find the root namespace.');
            return $this->laravel->getNamespace();
        }
    }


    /**
     * Parse the class name and format according to the root namespace.
     *
     * @param  string  $name
     * @return string
     */
    protected function qualifyClass($name, $type)
    {
        $name = ltrim($name, '\\/');

        $rootNamespace = $this->rootNamespace($type);

        if (Str::startsWith($name, $rootNamespace)) {
            return $name;
        }

        $name = str_replace('/', '\\', $name);

        return $this->qualifyClass(
            trim($rootNamespace, '\\'), $type).'\\'.$name;
    }

    protected function getClassNamespace($type)
    {
        $subfolder  = $this->getSubfolder($type);
        return $this->qualifyClass($this->getNameInput(), $type).$subfolder;
    }


    protected function getUnitName($name)
    {
        $name = str_replace('\\', '/', $name);
        $array = explode('/', $name);
        return trim(array_values(array_slice($array, -1))[0]);
    }

    public function getSubfolder($type)
    {
        $subfolder = config("generator.{$type}.subfolder");
        return empty($subfolder) ? '' : '\\'.trim($subfolder, '\\/');
    }


    public function makeAdditionalDirectory($type, $path)
    {
        // 目前僅用於 Search Service 需額外建立 IFilters 用
        $folder = config("generator.{$type}.create_folder");
        if(!empty($folder = trim($folder, '\\/'))) {
            $path .= '/'.$folder;
            if (!file_exists($path)) {
                return mkdir($path, 0777, true);
            }
        }
        return true;
    }
 }
