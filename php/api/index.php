<?php
define('IN_SYS', true);

// 指定允许其他域名访问
header('Access-Control-Allow-Origin:*');
// 响应类型
header('Access-Control-Allow-Methods:GET,POST,OPTIONS');
// 响应头设置
header('Access-Control-Allow-Headers:x-requested-with,content-type');

header('Content-type: application/json; charset=utf-8');

// SPA应用
//include_once ('connect.php'); //连接数据库
/*$con = mysql_connect('127.0.0.1:3306','root','123654');
if(!$con) exit('Error !');
mysql_select_db('sql_vue_todolist', $con);
mysql_query('set character_set_client = utf8');
mysql_query('set character_set_connection = utf8');
mysql_query('set character_set_results = utf8');*/

@$tab = $_GET['tab'];
@$act = $_GET['act'];
if($tab.$act=='') exit('vue spa api');

/**
 *
 * 获取远程内容
 * @param $url 接口url地址
 * @param $timeout 超时时间
 */
function pc_file_get_contents($url, $timeout=30) {
	$stream = stream_context_create(array('http' => array('timeout' => $timeout)));
	return @file_get_contents($url, 0, $stream);
}

function http_request($url,$timeout=30,$header=array()){
	if (!function_exists('curl_init')) {
		throw new Exception('server not install curl');
	}
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HEADER, true);
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
	if (!empty($header)) {
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	}
	$data = curl_exec($ch);
	list($header, $data) = explode("\r\n\r\n", $data);
	$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	if ($http_code == 301 || $http_code == 302) {
		$matches = array();
		preg_match('/Location:(.*?)\n/', $header, $matches);
		$url = trim(array_pop($matches));
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, false);
		$data = curl_exec($ch);
	}

	if ($data == false) {
		curl_close($ch);
	}
	@curl_close($ch);
	return $data;
}

function http_curl_request($url,$timeout=30){
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,$url);
	curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); //不直接输出
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	$result = curl_exec($ch);
	return $result;
}

function raw_input_stream(){
	$raw_post_data = file_get_contents('php://input', 'r');
	return json_decode($raw_post_data, true);
}

function fiteval($json){
	return '('.json_encode($json).')';
}

function resmsg($msg, $status = true){
	exit(json_encode(array(
		'msg' => $msg,
		'success' => $status
	)));
}

if($tab=='topics_latest'){
	echo '{"success":true,"data":';
	echo http_curl_request('https://www.v2ex.com/api/topics/latest.json');
	echo '}';
	exit();
}

if($tab=='topics_hot'){
	echo '{"success":true,"data":';
	echo http_curl_request('https://www.v2ex.com/api/topics/hot.json');
	echo '}';
	exit();
}

if(strstr($tab,'topics_show_')){
	$tab=str_replace('topics_show_', '', $tab);
	echo '{"success":true,"data":';
	echo http_curl_request('https://www.v2ex.com/api/topics/show.json?node_name='.$tab);
	echo '}';
	exit();
}

if(strstr($act,'show_id_')){
	$act=str_replace('show_id_', '', $act);
	echo '{"success":true,"data":';
	echo http_curl_request('https://www.v2ex.com/api/topics/show.json?id='.$act);
	echo '}';
	exit();
}

if(strstr($act,'replies_show_')){
	$act=str_replace('replies_show_', '', $act);
	echo '{"success":true,"data":';
	echo http_curl_request('https://www.v2ex.com/api/replies/show.json?page_size=20&topic_id='.$act.'&page=1');
	echo '}';
	exit();
}
?>