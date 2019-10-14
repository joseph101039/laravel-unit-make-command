# Unit Make Command

<p>A laravel unit make command which may help project team member quickly create the necessary files for a resource route.</p>

## 1. 安裝步驟
    
    
### (1)修改 composer.json:
在專案根目錄 composer.json 添加 property **"repositories"**，指定安裝庫，並在 require 中添加 **rdm/make-unit-command: ~1.0** (表示2.0 以前的 1.* 版本的更新都會被安裝)，版本可自由修改，添加內容如下斜線粗體部分:



<pre>
"license": "MIT",   
"type": "project",  
<em><strong>"repositories": [  
{  
        "type": "vcs",  
        "url": "git@34.80.61.76:rdm/unit-make-command.git"  
    }  
],</strong></em>  
"require": {  
    "php": ">=7.1.3",  
    ....  
    <em><strong>"rdm/make-unit-command": "~1.0"</strong></em>  
},
</span>
</pre>



### (2)同樣修改 composer.json:
在專案根目錄 composer.json 的 property **"config"** 中添加 **gitlab-domains** 指定安裝庫網域，以及 **gitlab-token** 屬性，指定存取該網域的access token，token 屬性為 gitlab user profile 底下的 personal_access_tokens，應更換為 guest user 的permission token，避免安全性問題，添加內容如下斜線粗體部分:


<pre>
"config": {  
    "preferred-install": "dist",  
    "sort-packages": true,  
    "optimize-autoloader": true,  
    <em><strong>
    "gitlab-domains": [  
        "34.80.61.76:8085"  
    ],  
    "gitlab-token": {  
        "34.80.61.76:8085": "saRpbZGoTaMqHzu5fi5_"  
    }  
</strong></em>
}  
</pre>


### (3) composer update
打開命令提示視窗，在專案根目錄路徑底下輸入 composer update 或是 composer install，確認安裝成功後

### (4) 添加 Service Provider
在專案根目錄 config/app.php 中找到 **providers** 陣列 在陣列中添加 **RDM\MakeUnitCommand\UnitCommandServiceProvider::class**，添加內容如下斜線粗體部分:

<pre>
'providers' => [  

    /*  
     * Laravel Framework Service Providers...  
     */  
     
     ...  
    App\Providers\RouteServiceProvider::class,  
    <em><strong>RDM\MakeUnitCommand\UnitCommandServiceProvider::class,</strong></em>  
],
</pre>


### (5) 複製套件文檔至專案中
打開命令提示視窗，在專案根目錄路徑底下輸入 **php artisan vendor:publish --tag=generator --force**，將必要檔案複製到專案中，若是沒有顯示錯誤，表示安裝完成。
不要忘了將這些檔案 git add，push 上 server 讓專案成員可以使用。
  

------------------------------------------------------------------------------------------------------
------------------------------------------------------------------------------------------------------
  
## 2. 使用說明:
### (1) 指令
用法類似於 Laravel 內建的 make:controller的指令。  
打開命令提示視窗，在專案根目錄路徑底下輸入，在專案根目錄底下輸入 **php artisan make:unit Folder/User** ，表示在 Folder 資料夾底下建立相關文件。

上述指令會  
(1) 在 *app/Http/Folder/Controller/User*底下建立 Controller.php, Transform.php, Form.php  
(2) 在*App/Management/Folder/User* 底下建立 Service.php, SearchService.php, Repository.php, Entity.php  
(3) 在 *api.php* 底下新增預設的 resource routes 如下:  

<pre>
// TODO: 檢查 User 建立 Routes URI 是否正確
Route::get('user', 'Folder\User\Controller@index');
Route::post('user', 'Folder\User\Controller@store');
Route::put('user/{user}', 'Folder\User\Controller@update');
Route::delete('user/{user}', 'Folder\User\Controller@destroy');
</pre>
 
   * 特別注意的是程式會自動檢測 URI 格式重複的 route 不會被建立，例如若是 api.php 中已經有*Route::put('user/{id}, ...)*，為了避免路由覆蓋的問題，*Route::put('user/{user}', 'User\Controller@update')* 將不會被建立。   

### (2) 啟用/停用建立檔案
若是不想自動在 *api.php* 底下新增 resource routes，可以到*config/generator.php* 底下找到**'route'  將 enable 改為 false**。
同理，你也可以disable controller, entity 或是 search 檔案的建立。


## 3. 更新步驟:
### 指令
1.  composer update  
2.  在專案根目錄路徑底下輸入 *php artisan vendor:publish --tag=generator --force* 覆蓋舊檔案  
3.  將所有變更 git push  

------------------------------------------------------------------------------------------------------
------------------------------------------------------------------------------------------------------

## Release Notes
### v1.1  
*2019-10-14*  
Added
+ 在 controller 添加 transformer 以及 Search 類別的宣告  
+ 添加輸入參數字首是否為大寫的檢查，若是小寫會詢問。 

Changed  
+ 修正 command help 描述  
+ 修改 shell 輸出訊息顏色  

