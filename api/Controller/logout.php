<?php
class logout extends G {
	public static function show() {
		session_start();
		unset($_SESSION['uid']);
		unset($_SESSION['username']);
		echo json_encode(['status' => 200, 'message' => '退出登录成功']);
	}
}