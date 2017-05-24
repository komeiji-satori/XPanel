<?php
date_default_timezone_set('Asia/Shanghai'); 
define('IN_APP', 1);
define('Debug_Mode', true);
if (!Debug_Mode) {
	error_reporting(0);
}
include 'base/G.php';
include 'base/ActiveRecord.php';
include 'config.php';
include 'base/QRCode.php';
include 'base/CheckCode.php';
ActiveRecord::setDb(new PDO("mysql:host=" . $db['ip'] . ";port=" . $db['port'] . ";dbname=" . $db['dbname'] . "", $db['user'], $db['pass']));
$Router = @G::GetURLRewriteArray($_SERVER['PATH_INFO']);
$Controller = $Router['action'];
@include 'Controller/' . $Controller . '.php';
if (!class_exists($Controller)) {
	G::show_err(404, 'Controller Not Found or Not Defined');
	exit;
}
$App = new $Controller();
$App->show($_REQUEST, $_SERVER, $Router);