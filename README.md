# 关于7723接口网关openApi(SDK)


仅支持post|get方法

本SDK应用场景服务端调用

Major类的构造器参数：$app_id,$app_secret,$gate_way_url,$api_ver
即：应用ID,应用密钥,接口网关地址(由接口文档提供),接口版本目录(默认v3)

###使用实例如下：
```php
define('ROOT_PATH',__DIR__.'');

require_once(ROOT_PATH.'/vendor/autoload.php');

use \BaZhangApiTools\Major;

$r = new Major('xxxxx','xxxx','http://gateway.dev.7723.com/index.php');


echo "<pre>";
// get使用方法一
// var_dump($r->app__i('get',['id'=>1,'fields' =>'*'],['version'=>'3.0.0']));
// get使用方法二
// $r->setDefaultJsonDecoder(true);
// var_dump($r->app__i('get',['id'=>1,'fields' =>'*']));
// get使用方法三
// var_dump($r->setDefaultJsonDecoder(true)->app__i('get',['id'=>1,'fields' =>'*']));

// post使用方法一
// var_dump($r->app__a('post',['app_id' => '7723cn_android_phone_1S2','iv'=>'0123456789012345']));
// post使用方法二
// $r->setDefaultJsonDecoder(true);
// var_dump($r->app__a('post',['app_id' => '7723cn_android_phone_1S2','iv'=>'0123456789012345']));
// post使用方法三
// var_dump($r->setDefaultJsonDecoder(true)->app__a('post',['app_id' => '7723cn_android_phone_1S2','iv'=>'0123456789012345']));

/**
 * 注：
 * 接口调用方法参数分别：string请求类型[get|post]，array请求内容[必选]，array请求头[可选]
 */

```
>[warning] php5.5 以下版 文件上传文件路径使用@符号进行处理