<?php
if (!defined('IN_APP')) {
	echo json_encode(['status' => 403, 'message' => 'Not in Application']);
	exit;
}
class G {
	public static function GetURLRewriteArray($url_arr) {
		$url = explode('/', $url_arr);
		unset($url[0]);
		$url = array_values($url);
		$url_array = [];
		for ($i = 0; $i < count($url); $i++) {
			if ($i % 2 == 0) {
				$url_array[$url[$i]] = $url[$i + 1];
			}
		}
		return $url_array;
	}
	public static function show_err($code = 0, $message = '', $data = []) {
		if (empty($data)) {
			echo json_encode(array('status' => $code, 'message' => $message));
		} else {
			echo json_encode(array('status' => $code, 'message' => $message, 'result' => $data));
		}
	}
	public static function show_msg($code = 0, $message = '', $data = []) {
		if (empty($data)) {
			echo json_encode(array('status' => $code, 'message' => $message));
		} else {
			echo json_encode(array('status' => $code, 'message' => $message, 'result' => $data));
		}
	}
	public static function object2array($obj) {
		return (array) $obj;
	}
	public static function array2object($array) {
		return (object) $array;
	}
}
class Tpl{
	public static function header($title=''){
		$source = file_get_contents('Template/common/header.tpl');
		$str = str_replace('{{title}}', $title, $source);
		echo $str;
	}
	public static function main($right_panel=[],$title='',$content=''){
		$right_source = '';
		for ($i=0; $i < count($right_panel); $i++) {
			if ($right_panel[$i]['is_active']==1) {
				$right_source .= '<li class="topNavi active"><a href="'.$right_panel[$i]['href'].'"><span class="'.$right_panel[$i]['icon'].'"></span>'.$right_panel[$i]['name'].'</a></li>'.PHP_EOL;
			}else{
				$right_source .= '<li class="topNavi"><a href="'.$right_panel[$i]['href'].'"><span class="'.$right_panel[$i]['icon'].'"></span>'.$right_panel[$i]['name'].'</a></li>'.PHP_EOL;
			}
		}
		$source =  file_get_contents('Template/common/main.tpl');
		$str = str_replace('{{sidenav}}', $right_source, $source);
		$str = str_replace('{{title}}', $title, $str);
		$content = file_get_contents('Template/'.$content.".tpl");
		$str = str_replace('{{content}}', $content, $str);
		echo $str;
	}
	public static function footer(){
		echo file_get_contents('Template/common/footer.tpl');
	}
}