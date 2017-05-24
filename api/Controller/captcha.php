<?php
class captcha extends G {
	public static function show() {
		CheckCode::$useNoise = true;
		CheckCode::$useCurve = true;
		CheckCode::entry();
	}
}