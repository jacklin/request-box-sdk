<?php 
namespace BaZhangApiTools; 

use Curl\Curl;
use BaZhangApiTools\RequestContent;
/**
 * 请求客户端
 */
class RequestClient
{
	private $curl;

	private $apiUrl; //接口地址 

	private $apiVer; //接口版本

	private $apiRequestType = 'get' ; //接口请示方式 目录仅支持get\post

	private $error; //接收错误信息

	private $response = NULL; //响应数据

	private $follow303WithPost = false; //默认301与302 POST不跟随请求

	public function __construct($api_request_type, $api_url, $api_ver='v3'){
		$this->apiRequestType = $api_request_type;
		$this->apiUrl = $api_url;
		$this->apiVer = $api_ver;
		$this->curl = $this->getCurl();
	}
	/**
	 * 设置支持post跟随请求
	 * BaZhang Platform
	 * @Author   Jacklin@shouyiren.net
	 * @DateTime 2021-07-14T10:23:40+0800
	 * @param    boolean                  $follow_303_with_post 默认false 不跟随,true跟随
	 */
	public function setFollow303WithPost($follow_303_with_post=false){
		$this->follow303WithPost = $follow_303_with_post;
		return $this;
	}
	public function request($request){
		$api_uri = $request->getApiUri();
		$request_url = $this->parseApiUrl($api_uri);
		if ($request_url !== false) {
			$this->getCurl()->setHeaders($request->getRequestHeader());
			switch (strtolower($this->apiRequestType)) {
				case 'post':
					$this->response[$api_uri] = $this->getCurl()->post($request_url, $request->getRequestBody(),$this->follow303WithPost);
					break;
				default:
					$this->response[$api_uri] = $this->getCurl()->get($request_url, $request->getRequestBody());
					break;
			}
			if ($this->getCurl()->error) {
				$this->error[$api_uri] = $this->getCurl()->error;
			}
		}
		return $this;
	}
	/**
	 * 设置请求类型
	 * BaZhang Platform
	 * @Author   Jacklin@shouyiren.net
	 * @DateTime 2017-03-07T10:20:00+0800
	 * @version  [3.0.0]
	 * @param    string                   $type get|post
	 */
	public function setApiRequestType($type){
		$this->apiRequestType = $type;
		return $this;
	}
	/**
	 * 获取所有响应数据
	 * BaZhang Platform
	 * @Author   Jacklin@shouyiren.net
	 * @DateTime 2017-03-06T14:18:06+0800
	 * @version  [3.0.0]
	 * @param    string                   $api_uri 接口uri
	 * @return   mixed                             接口响应数据
	 */
	public function response($api_uri=''){
		if (empty($api_uri)) {
			return reset($this->response); 
		}
		return isset($this->response[$api_uri]) ? $this->response[$api_uri] : array();
	}
	/**
	 * 获取请求的接口URL
	 * BaZhang Platform
	 * @Author   Jacklin@shouyiren.net
	 * @DateTime 2017-03-06T14:13:20+0800
	 * @version  [3.0.0]
	 * @param    [type]                   $api_uri [description]
	 * @return   [type]                            [description]
	 */
	private function parseApiUrl($api_uri){
		if (strpos($api_uri, '.')) {
			$api_uri = str_replace('.', '/', $api_uri);
		}else if (strpos($api_uri, '/')) {
			
		}else{
			$this->error = "uri 格式错误";
			return false;
		}
		return $this->apiUrl.'/'.$this->apiVer.'/'.$api_uri;
	}
	/**
	 * 获取请求错误
	 * BaZhang Platform
	 * @Author   Jacklin@shouyiren.net
	 * @DateTime 2017-03-06T14:25:20+0800
	 * @version  [3.0.0]
	 * @param    string                   $api_uri 接口uri
	 * @return   [type]                   [description]
	 */
	public function getError($api_uri=''){
		if (empty($api_uri)) {
			return reset($this->error); 
		}
		return isset($this->error[$api_uri]) ? $this->error[$api_uri] : array();
	}
	public function __call($name, $arguments){
		if (method_exists($this->getCurl(), $name)) {
			return call_user_func_array(array($this->getCurl(),$name), $arguments);
		}else{
			throw new \Exception("请求方法名格式不存在");
		}
	}
	/**
	 * 获取curl实例
	 * BaZhang Platform
	 * @Author   Jacklin@shouyiren.net
	 * @DateTime 2021-07-14T11:52:38+0800
	 * @return   [type]                   [description]
	 */
	private function getCurl(){
		if ((version_compare(PHP_VERSION, '5.5.11') < 0) || defined('HHVM_VERSION')) {
				$this->curl = new Curl();
		}else{
			if ($this->curl instanceof Curl ) {
				
			}else{
				$this->curl = new Curl();
			}
		}
		return $this->curl;
	}
}