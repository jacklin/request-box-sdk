<?php 
namespace BaZhangApiTools; 
/**
* 请求内容
*/
use Tool\Sign;
class RequestContent
{

	private $appId; //应用ID
	private $appSecret; //应用密钥

	private $apiUri; //uri   如：app/i

	private $error; //接收错误信息

	private $headerData;

	private $bodyData;

	public function __construct($app_id, $app_secret, $api_uri){
		$this->appId = $app_id;
		$this->appSecret = $app_secret;
		$this->apiUri = $api_uri;
		$this->setHeaderData();
	}
	public function setHeaderData($data=array()){
		$this->headerData['app-id'] = isset($data['app-id']) ? $data['app-id'] : $this->appId;
		$this->headerData['did'] = isset($data['did']) ? $data['did'] : PHP_OS;
		$this->headerData['encypt-did'] = isset($data['encypt-did']) ? $data['encypt-did'] : '';
		$this->headerData['version'] = isset($data['version']) ? $data['version'] : 3;
		$this->headerData['version-mini'] = isset($data['version-mini']) ? $data['version-mini'] : '';
		$this->headerData['nonce-str'] = isset($data['nonce-str']) ? $data['nonce-str'] : $this->generateRandomString();
		$this->headerData['time-stamp'] = isset($data['time-stamp']) ? $data['time-stamp'] : time();
		$this->headerData['format'] = isset($data['format']) ? $data['format'] : 'json';
		$this->headerData['enable-base64'] = isset($data['enable-base64']) ? : '';
		$this->headerData['sign-type'] = isset($data['sign-type']) ? $data['sign-type'] : '';
		$this->headerData['charset'] = isset($data['charset']) ? $data['charset'] : 'utf8';
		$this->headerData['method'] = isset($data['method']) ? $data['method'] : strpos('/', $this->apiUri) ? str_replace('/', '.', $this->apiUri) : $this->apiUri;
		$this->headerData = array_merge($this->headerData,$data);
		return $this;
	}
	public function setBodyData($data=array()){
		$this->bodyData = $data;
		$this->bodyData['sign'] = $this->sign();
		return $this;
	}
	/**
	 * 签名方法
	 * BaZhang Platform
	 * @Author   Jacklin@shouyiren.net
	 * @DateTime 2017-03-06T11:35:37+0800
	 * @version  [3.0.0]
	 * @return   string                   签结果
	 */
	private function sign(){
		$sign = new Sign();
		//添加判断请求参数是否包含文件流，文件流不参与签名
		$request_data = $this->bodyData;
		foreach ($request_data as $key => $value) {
		    if (is_string($value) && strpos($value, '@') === 0 && is_file(substr($value, 1))) {
		    	unset($request_data[$key]);
		    }
		}
		//过滤头部不签名的字段
		$unforce = array('encypt-did','version-mini','format','enable-base64','sign-type','charset');//非必填
		$force = array('app-id','did','version','nonce-str','time-stamp','method');////必填
		$_param = array_merge($unforce,$force);
		$header_data=array();
		foreach ($_param as $key => $value) {
			if ($this->headerData[$value] !== null) {
				$header_data[$value] = $this->headerData[$value];
			}
		}

		return $sign->setKey($this->appSecret)->generateSign(array_filter(array_merge($header_data,$request_data),function($value){
			return !is_array($value);
		})
	);
	}
	/**
	 * 生成随机数
	 * BaZhang Platform
	 * @Author   Jacklin@shouyiren.net
	 * @DateTime 2017-03-06T11:34:45+0800
	 * @version  [3.0.0]
	 * @return   string                  生成随机数md5格式
	 */
	private function generateRandomString(){
		return md5(time().mt_rand(100000, 999999));
	}
	/**
	 * 获取接口Uri
	 * BaZhang Platform
	 * @Author   Jacklin@shouyiren.net
	 * @DateTime 2017-03-06T11:45:47+0800
	 * @version  [3.0.0]
	 * @return   string                   接口Uri
	 */
	public function getApiUri(){
		return $this->apiUri;
	}
	/**
	 * 获取请求头数据
	 * BaZhang Platform
	 * @Author   Jacklin@shouyiren.net
	 * @DateTime 2017-03-06T11:51:31+0800
	 * @version  [3.0.0]
	 * @return   array                   请求头数据
	 */
	public function getRequestHeader(){
		return $this->headerData;
	}
	/**
	 * 获取请求数据
	 * BaZhang Platform
	 * @Author   Jacklin@shouyiren.net
	 * @DateTime 2017-03-06T11:51:31+0800
	 * @version  [3.0.0]
	 * @return   array                   请求数据
	 */
	public function getRequestBody(){
		return $this->bodyData;
	}

}