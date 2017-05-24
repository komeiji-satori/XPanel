<?php
class register extends G {
	public static function show($params, $server) {
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
			echo json_encode(['status' => 403, 'message' => '请先退出登录']);
			return;
		}
		$username = addslashes($params['username']);
		$NameRegex = '(^[a-zA-Z0-9_-]{3,16}$)';
		if (!preg_match($NameRegex, $username)) {
			unset($_SESSION['checkcode']);
			echo json_encode(['status' => 403, 'message' => '用户名不符合规则:' . $NameRegex]);
			return;
		}
		$password = md5($params['password']);
		$user = new user();
		$balance = new balance();
		$user = $user->select('id,username,password')->eq("username", $username)->find();
		$result = @G::object2array($user)['data'];
		if ($result) {
			unset($_SESSION['checkcode']);
			unset($user);
			$result = [];
			$result['status'] = 403;
			$result['message'] = '用户已存在';
			echo json_encode($result);
			return;
		}
		$user = new user();
		$user->username = $username;
		$user->password = $password;
		$user->regtime = time();
		$user->regip = $_SERVER['REMOTE_ADDR'];
		$balance->username = $username;
		$balance->balance = 30;
		$user->insert();
		$balance->insert();

		unset($_SESSION['checkcode']);
		$_SESSION['username'] = $username;
		$_SESSION['uid'] = $user->data['id'];
		echo json_encode(['status' => 200, 'message' => '注册成功', 'username' => $username, 'uid' => intval($user->data['id'])]);
	}
}