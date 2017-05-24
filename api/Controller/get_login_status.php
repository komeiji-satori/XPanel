<?php
class get_login_status extends G {
	public static function show() {
		session_start();
		$result = [];
		if (!isset($_SESSION['uid'])) {
			$result['status'] = 401;
			$result['username'] = '未登录';
			$result['message'] = '请先登录或注册';
			echo json_encode($result);
		} else {
			$result['status'] = 200;
			$result['username'] = $_SESSION['username'];
			$result['uid'] = $_SESSION['uid'];
			$balance = new balance();
			$user = new user();
			$user = $user->select('regtime,regip,invite_limit,isLogged,is_genuine')->eq("id", $result['uid'])->find();
			$user = @G::object2array($user)['data'];
			$balance = $balance->select('username,balance')->eq("username", $_SESSION['username'])->find();
			$result['balance'] = @G::object2array($balance)['data']['balance'];
			$result['regtime'] = date('Y年m月d日 H:i:s', $user['regtime']);
			$result['regip'] = $user['regip'];
			$result['is_genuine'] = intval($user['is_genuine']);
			if ($user['isLogged'] == 0) {
				$result['isLogged'] = '离线';
			} else {
				$result['isLogged'] = '在线';
			}
			echo json_encode($result);
		}
	}
}