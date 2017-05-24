<?php
if (!defined('IN_APP')) {
	echo json_encode(['status' => 403, 'message' => 'Not in Application']);
	exit;
}
$db = array(
	'ip' => 'localhost',
	'port' => 3306,
	'user' => 'root',
	'pass' => 'root',
	'dbname' => 'xpanel',

);
class user extends ActiveRecord {
	public $table = 'user';
	public $primaryKey = 'id';
}
class balance extends ActiveRecord {
	public $table = 'balance';
	public $primaryKey = 'id';
}