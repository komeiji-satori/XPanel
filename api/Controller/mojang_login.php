<?php
class mojang_login extends G {
	public static function show($params) {
		session_start();
		if (isset($_SESSION['uid'])) {
			unset($_SESSION['checkcode']);
			echo json_encode(['status' => 403, 'message' => '重复登陆']);
			return;
		}
		$user = new user();
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
			echo json_encode(['status' => 400, 'message' => "尝试与Mojang认证服务器交互时发生错误:" . $result['errorMessage']]);
			return;
		}
		$auth_username = $result['selectedProfile']['name'];
		$user_exists = $user->select('*')->eq('username', $auth_username)->find();
		$user_data = G::object2array(@$user_exists)['data'];
		if (!$user_exists) {
			echo json_encode(['status' => 403, 'message' => "登录失败:请先使用您的Mojang帐号注册"]);
			return;
		} else {
			if ($user_data['is_genuine'] == 1) {
				$_SESSION['username'] = $user_data['username'];
				$_SESSION['uid'] = $user_data['id'];
				echo json_encode(['status' => 200, 'message' => '登陆成功', 'username' => $user_data['username'], 'uid' => $user_data['id']]);
			} else {
				echo json_encode(['status' => 400, 'message' => '登录失败:发生未知错误']);
			}
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