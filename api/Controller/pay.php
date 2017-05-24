<?php
class pay extends G {
	public static function show($params, $server, $router) {
		if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
			echo json_encode(['status' => 405, 'message' => '不允许的请求方式']);
			return;
		}
		session_start();
		if (!isset($_SESSION['uid'])) {
			echo json_encode(['status' => 401, 'message' => '未登录']);
			return;
		}
		$checkcode = strtoupper($params['checkcode']);
		if (!isset($params['checkcode']) || $checkcode !== @$_SESSION['checkcode']) {
			unset($_SESSION['checkcode']);
			echo json_encode(['status' => 403, 'message' => '验证码错误']);
			return;
		}
		$self_username = $_SESSION['username'];
		$to_username = $params['username'];
		$NameRegex = '(^[a-zA-Z0-9_]{3,16}$)';
		if (!preg_match($NameRegex, $to_username)) {
			unset($_SESSION['checkcode']);
			echo json_encode(['status' => 403, 'message' => '用户名不符合规则:' . $NameRegex]);
			return;
		}
		if (!self::check_user_exists($to_username)) {
			unset($_SESSION['checkcode']);
			echo json_encode(['status' => 404, 'message' => '用户不存在']);
			return;
		}
		if (strtoupper($self_username) == strtoupper($to_username)) {
			unset($_SESSION['checkcode']);
			echo json_encode(['status' => 403, 'message' => '不能转账给自己！']);
			return;
		}
		unset($_SESSION['checkcode']);
		$transfer_balance = intval($params['balance']);
		echo json_encode(self::transfer_money($self_username, $to_username, $transfer_balance));
	}
	public static function get_user_balance($username) {
		$balance = new balance();
		$balance = $balance->select('*')->eq("username", $username)->find();
		$result = @G::object2array($balance)['data'];
		if (!$result) {
			return false;
		} else {
			return $result['balance'];
		}
	}
	public static function transfer_money($self_username, $to_username, $balance) {
		@session_start();
		$self_balance = self::get_user_balance($self_username);
		$to_balance = self::get_user_balance($to_username);
		if ($balance < 0 || empty($balance)) {
			unset($_SESSION['checkcode']);
			return ['status' => 403, 'message' => '转账金额错误！'];
		}
		if ($self_balance - $balance < 0) {
			unset($_SESSION['checkcode']);
			return ['status' => 403, 'message' => '余额不足'];
		}
		$self_username_object = new balance();
		$self_username_object = $self_username_object->select('*')->eq('username', $self_username)->find();
		$self_username_object->balance = $self_balance - $balance;
		//return $self_username_object->update();
		$to_username_object = new balance();
		$to_username_object = $to_username_object->select('*')->eq('username', $to_username)->find();
		$to_username_object->balance = $to_balance + $balance;
		$self_username_object->update();
		$to_username_object->update();
		//return $self_balance - $balance;
		$return = G::object2array($self_username_object)['data'];
		if (!$return) {
			unset($_SESSION['checkcode']);
			return ['status' => 500, 'message' => '数据库错误:转账失败'];
		}
		$result = [];
		$result['status'] = 200;
		$result['message'] = '转账成功';
		$result['balance'] = $return['balance'];
		return $result;
	}
	public static function check_user_exists($username) {
		$user = new user();
		$user = $user->select('id')->eq("username", $username)->find();
		$result = @G::object2array($user)['data'];
		if (!$result) {
			return false;
		} else {
			return true;
		}
	}
}