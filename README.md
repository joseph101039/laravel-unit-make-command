# unit-make-command

A laravel unit make command which may help project team member quickly create the necessary files for a resource route.


1. 安裝步驟:

(1) 在專案根目錄 composer.json 添加 property **"repositories"**，並在 require 中添加rdm/make-unit-command: ~1.0 ，
版本可自由修改，如下斜線部分:
<br>
<br>
"license": "MIT",
"type": "project",
*"repositories": [
    {
        "type": "vcs",
        "url": "git@34.80.61.76:rdm/unit-make-command.git"
    }
],*

"require": {
    "php": ">=7.1.3",
    ....
    *"rdm/make-unit-command": "~1.0"*
},


(2) 在 project main composer.json 修改 property **"config"**，並在 config 中添加 **gitlab-domains** 
以及 **gitlab-token** 屬性，token 屬性為 gitlab user profile 底下的 personal_access_tokens，
應更換為 guest permission token，如下斜線部分:


"config": {

    "preferred-install": "dist",
    "sort-packages": true,
    "optimize-autoloader": true,
    *
    "gitlab-domains": [
        "34.80.61.76:8085"
    ],
    "gitlab-token": {
        "34.80.61.76:8085": "saRpbZGoTaMqHzu5fi5_"
    }
    *
    
}



(3) 打開命令提示視窗，在專案根目錄輸入 composer update 或是 composer install，確認安裝成功後

(4) 在專案根目錄 config/app.php 中找到 **providers** 陣列 在陣列中添加 **RDM\MakeUnitCommand\UnitCommandServiceProvider::class**，如下斜線部分:


'providers' => [

    /*
     * Laravel Framework Service Providers...
     */
     
     ...
    App\Providers\RouteServiceProvider::class,
    RDM\MakeUnitCommand\UnitCommandServiceProvider::class,
],

(5) 打開命令提示視窗，在專案根目錄輸入 **php artisan vendor:public --tag=generator --force**，若是沒有顯示錯誤，表示安裝完成。




