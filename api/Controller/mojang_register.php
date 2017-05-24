<?php
class mojang_register extends G {
	public static function show($params) {
		session_start();
		$user = new user();
		$balance = new balance();
		$address = 'https://authserver.mojang.com/';
		$payloads = json_encode([
			'agent' => [
				'name' => 'Minecraft',
				'version' => 1,
			],
			'username' => @$params['email'],
			'password' => @$params['password'],
			'clientToken' => rand(1000, 9999),
		]);
		$result = json_decode(self::API_Post($payloads, $address . "authenticate"), true);
		if (@$result['error']) {
			echo json_encode(['status' => 400, 'message' => "尝试与Mojang认证服务器交互时发生错误:" . $result['errorMessage'], 'payloads' => $payloads]);
			return;
		}
		$auth_username = $result['selectedProfile']['name'];
		$user_exists = $user->select('*')->eq('username', $auth_username)->find();
		$user_data = G::object2array(@$user_exists)['data'];
		if ($user_exists) {
			if (@$user_data['is_genuine'] == 0) {
				echo json_encode(['status' => 403, 'message' => "同名的用户名已存在,请尝试更改您的正版账户用户名"]);
				return;
			}
		} else {
			$game_password = $params['game_password'];
			if (strlen($game_password) == 0 || strlen($game_password) < 4) {
				echo json_encode(['status' => 400, 'message' => "密码过短"]);
				return;
			}
			$user->username = $auth_username;
			$user->password = md5($game_password);
			$user->regtime = time();
			$user->is_genuine = 1;
			$user->regip = $_SERVER['REMOTE_ADDR'];
			$balance->username = $auth_username;
			$balance->balance = 30;
			$user->insert();
			$balance->insert();
			//unset($_SESSION['checkcode']);
			$_SESSION['username'] = $auth_username;
			$_SESSION['uid'] = $user->data['id'];
			echo json_encode(['status' => 200, 'message' => '注册成功', 'username' => $auth_username, 'uid' => intval($user->data['id'])]);
		}

	}
	private static function API_Post($data, $url) {
		$header = ['content-type: application/json'];
		$UA = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/56.0.2924.87 Safari/537.36';
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_USERAGENT, $UA);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		$response = curl_exec($ch);
		curl_close($ch);
		return $response;
	}
}