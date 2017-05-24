<?php
class login extends G {
	public static function show($params, $server, $router) {
		if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
			echo json_encode(['status' => 405, 'message' => '不允许的请求方式']);
			return;
		}
		session_start();
		$checkcode = strtoupper($params['checkcode']);
		if (!isset($params['checkcode']) || $checkcode !== @$_SESSION['checkcode']) {
			unset($_SESSION['checkcode']);
			echo json_encode(['status' => 403, 'message' => '验证码错误']);
			return;
		}
		if (isset($_SESSION['uid'])) {
			unset($_SESSION['checkcode']);
			echo json_encode(['status' => 403, 'message' => '重复登陆']);
			return;
		}
		$username = addslashes($params['username']);
		$password = md5($params['password']);
		$user = new user();
		$user = $user->select('id,username,password')->eq("username", $username)->find();
		$result = @G::object2array($user)['data'];
		if (!$result) {
			unset($_SESSION['checkcode']);
			$result = [];
			$result['status'] = 404;
			$result['message'] = '用户不存在';
			echo json_encode($result);
			return;
		}
		$user_password = $result['password'];
		if ($user_password == $password) {
			$user->lastlogin = time();
			$user->login_ip = $_SERVER['REMOTE_ADDR'];
			$user->update();
			$_SESSION['uid'] = intval($result['id']);
			$_SESSION['username'] = $result['username'];
			unset($_SESSION['checkcode']);
			echo json_encode(['status' => 200, 'message' => '登陆成功', 'username' => $result['username'], 'uid' => $result['id']]);
			return;
		} else {
			unset($_SESSION['checkcode']);
			echo json_encode(['status' => 403, 'message' => '密码错误']);
		}
	}
}