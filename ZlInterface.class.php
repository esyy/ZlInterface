<?php
/**
 * 洲立影院在线选座对接
 * @author          esyy <esyy@qq.com>
 * @since           2016-03-01
 * @version         1.3
 */
class ZlInterface{
	
	//loadCodeString
	private $loadCodeString = '#################';
	//用户名
	private $username = '#############';
	//密码
	private $password = '################';
	//公共地址
	//	private $api_url = "http://################cketweb/";
	private $api_url = 'http://####################ticketweb/';
	//查询座位售票情况
	private $getSeatplanUrl = 'getseatplan.aspx?CID=%s&SID=%s&u=%s&p=%s';
	//查询场次可用票种信息
	private $getTicketTypeUrl = 'gettickettype.aspx?CID=%s&SID=%s&u=%s&p=%s';
	//订座地址
	private $bookSeatUrl = 'bookseat.aspx?TI=%s&SI=%s&CID=%s&SID=%s&u=%s&p=%s';
	//确认订座地址
	private $confirmOrderUrl = 'ConfirmOrder.aspx?MCLBH=%s&PH=%s&RefClient=%s&u=%s&p=%s';
	//取消订座地址
	private $cancelOrderUrl = 'Cancelorder.aspx?MCLBH=%s&u=%s&p=%s';
	//查询订座信息
	private $queryOrderUrl = 'QueryOrder.aspx?MCLBH=%s&u=%s&p=%s';
	
	/**
	 * 查询座位售票
	 * @param int SID  场次ID
	 * @param int CID  戏院ID
	 * @param array $param 排期信息
	 * @return Ambigous <string, mixed>|multitype:string
	 */
	
	public function getSeatPlan($param = array()){
		if(!empty($param) && is_array($param) && isset($param['provider_cinema_id']) && isset($param['play_id'])){
			$url = sprintf($this->api_url.$this->getSeatplanUrl, $param['provider_cinema_id'], $param['play_id'], $this->username, $this->password);
		//	echo '获取座位信息地址：'.$url;
			return $this->http_post($url, '');
		}else {
			return json_encode(array('errorCode'=>'参数错误'));
		}
	}
	
	/**
	 * 获取场次票种信息
	 * @param array $param
	 * @return multitype:string
	 */
	public function getTicketType($param = array()){
		if( isset($param['cid']) && isset($param['sid'])){
			$url = sprintf($this->api_url.$this->getTicketTypeUrl, $param['cid'], $param['sid'], $this->username, $this->password);
		//	echo '查询票种信息地址：'.$url;
			return $this->http_post($url, '');
		}else{
			return json_encode(array('errorCode'=>'参数错误'));
		}
	}
	
	/**
	 * 预定座位
	 * @param array $param
	 * $param['ti'] 票种信息
	 * $param['si'] 座位信息
	 * $param['cid'] 影院ID
	 * $param['sid'] 场次ID
	 * @return multitype:string
	 */
	public function bookSeat($param = array()){
		if( isset($param['ti']) && isset($param['si']) && isset($param['cid']) && isset($param['sid']) ){
			$url = sprintf($this->api_url.$this->bookSeatUrl, $param['ti'], $param['si'], $param['cid'], $param['sid'], $this->username, $this->password);
		//	echo '预定座位地址：'.$url;
			return $this->http_post($url, '');
		}else {
			return json_encode(array('errorCode'=>'参数错误'));
		}	
	}
	
	/**
	 * 确认预定信息
	 * $param['mclbh'] 预定返回的识别编号（API提供）
	 * $param['ph'] 接受虚拟码手机
	 * $param['refClient'] 呼叫方流水号
	 * @param unknown $param
	 * @return Ambigous <string, mixed>|multitype:string
	 */
	public function confirmOrder($param = array()){
		if(isset($param['mclbh']) && isset($param['ph']) && isset($param['refClient'])){
			$url = sprintf($this->api_url.$this->confirmOrderUrl, $param['mclbh'], $param['ph'], $param['refClient'], $this->username, $this->password);
		//	echo '确认订座地址'.$url;
			return $this->http_post($url, '');
		}else{
			return json_encode(array('errorCode'=>'参数错误'));
		}
	}
	
	/**
	 * 取消订座
	 * $param['mclbh'] 预定返回的识别码
	 * @param array $param
	 * @return Ambigous <string, mixed>|multitype:string
	 */
	public function cancelOrder($param = array() ){
		if(isset($param['mclbh'])){
			$url = sprintf($this->api_url.$this->cancelOrderUrl, $param['mclbh'], $this->username, $this->password);
		//	echo '取消订单地址：'.$url;
			return $this->http_post($url, '');
		}else{
			return json_encode(array('errorCode'=>'参数错误'));
		}
	}
	
	/**
	 * 查询订单状态
	 * $param['mclbh'] 预定返回的识别码
	 * @param array $param['mclbh']
	 * @return multitype:string
	 */
	public function queryOrder($param = array() ){
		if(isset($param['mclbh'])){
			$url = sprintf($this->api_url.$this->queryOrderUrl, $param['mclbh'], $this->username, $this->password);
		//	echo '查询订单状态地址：'.$url;
			return $this->http_post($url, '');
		}else {
			return json_encode(array('errorCode'=>'参数错误'));
		}
	}
	
	/**
	 * 查询字符串在基础字符串的位置
	 * @param string $string
	 * @return mixed
	 */
	private function getIntValue($string = ''){
		return strpos($this->loadCodeString, $string);
	}
	
	/**
	 * 解密方法
	 * @param string $string 解密字符串
	 * @return number
	 */
	public function getRealValue($string = ''){
		$value = 0;
		$count =  intval(strlen($string));
		$loadCount = intval(strlen($this->loadCodeString));
		if(empty($string)){
			return -1;
		}else {
			for ($i=0; $i < intval(strlen($string)); $i++){
				$value += $this->getIntValue(substr($string, $count-$i-1, 1)) * pow($loadCount, $i);
			}
			return $value;
		}
	}

	/**
	 * 发送https请求，需要开启php_curl
	 * Enter description here ...
	 * @param unknown_type $url
	 * @param unknown_type $data
	 */
	public function http_post($url, $data){
	    $curl = curl_init();
	    curl_setopt($curl, CURLOPT_URL, $url); 
	    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
	    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
	    curl_setopt($curl, CURLOPT_POST, 1);
	    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	    $output = curl_exec($curl);
	    if (curl_errno($curl)) {
	       return 'Error: '.curl_error($curl);
	    }
	    curl_close($curl);
	    return $output;
	}
	
	/**
	 * get方式获取
	 * @param array or string $condition
	 * @return json array
	 */
	public function get_contents($query,$url,$params){
		$api_url = $this->api_url.$url."/?{$query}&_sig=".$params['_sig'];
		$rs_json = file_get_contents($api_url);
		return $rs_json;
	}
	
	/**
	 * 
	 * @param unknown $data
	 * @return unknown
	 */
	public function gzdecode ($data) {
		$flags = ord(substr($data, 3, 1));
		$headerlen = 10;
		$extralen = 0;
		$filenamelen = 0;
		if ($flags & 4) {
			$extralen = unpack('v' ,substr($data, 10, 2));
			$extralen = $extralen[1];
			$headerlen += 2 + $extralen;
		}
		if ($flags & 8) // Filename
			$headerlen = strpos($data, chr(0), $headerlen) + 1;
		if ($flags & 16) // Comment
			$headerlen = strpos($data, chr(0), $headerlen) + 1;
		if ($flags & 2) // CRC at end of file
			$headerlen += 2;
		$unpacked = @gzinflate(substr($data, $headerlen));
		if ($unpacked === FALSE)
			$unpacked = $data;
		return $unpacked;
	}
}
