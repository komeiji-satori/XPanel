<?php
class qr extends QRcode {
	public static function show($params) {
		QRCode::png($params['text'], false, 5, 3, 1);
	}
}