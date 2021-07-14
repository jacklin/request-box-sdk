<?php 
namespace BaZhangApiTools;

use BaZhangApiTools\RequestContent;
use BaZhangApiTools\RequestClient;
/**
* 主应用类
*/
class Major
{
	private $apiUrl; //接口地址 
	private $apiVer; //接口版本

	private $appId; //应用ID
	private $appSecret; //应用密钥

	private $error;

	private $client; //请求客户端

	public function __construct($app_id, $app_secret, $api_url, $api_ver = 'v3'){
		$this->appId = $app_id;
		$this->appSecret = $app_secret;
		$this->apiUrl = $api_url;
		$this->apiVer = $api_ver;
		$this->client = new RequestClient('get', $this->apiUrl, $api_ver=$this->apiVer);
	}
	public function __call($name, $arguments){
		$result = '';
		if (strpos($name,'__')) {
			$api_request_type = reset($arguments);//请求类型
			$this->client->setApiRequestType($api_request_type);
			$api_uri = str_replace('__', '/', $name);
			$second_argument = next($arguments);
			/**
			 * 请求内容
			 */
			$request_data = array();
			if (is_array($second_argument)) {
				$request_data = $second_argument;
			}else{
				throw new \Exception("请求数据格式不正确");
			}
			/**
			 * 请求头
			 */
			$request_head = array();
			$third_argument = next($arguments);
			if (is_array($third_argument)) {
				$request_head = $third_argument;
			}

			$content = new RequestContent($this->appId, $this->appSecret, $api_uri);
			$content->setHeaderData($request_head)->setBodyData($request_data);
			$result = $this->client->request($content)->response($api_uri);
			$this->error = $this->client->getError($api_uri);
		}else{
			call_user_func_array(array($this->client,$name), $arguments);
			return $this;
		}
		return $result;
	}
	/**
	 * 获取错误
	 * BaZhang Platform
	 * @Author   Jacklin@shouyiren.net
	 * @DateTime 2017-03-06T14:25:20+0800
	 * @version  [3.0.0]
	 * @return   [type]                   [description]
	 */
	public function getError(){
		return $this->error;
	}
}