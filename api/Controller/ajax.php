<?php
class ajax extends G {
	public static function show($params, $server, $router) {
		$action = @$params['type'];
		if (!isset($action)) {
			G::show_err(500, 'Server Error');
			return;
		}
		@require $action . ".php";
		$response = $action::init($params, $server, $router);
		echo json_encode($response);
	}
}