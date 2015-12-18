<?php
define('HHXC',       TRUE);
define('DEBUG',      TRUE);
define('DB_HOST',    '127.0.0.1');
define('DB_NAME',    'test');
define('DB_USER',    'root');
define('DB_PWD',     '');
define('DB_CHARSET', 'UTF-8');
define('CL_CHARSET', 'UTF-8');

require_once('common.php');
$mysql = StorageConnect('127.0.0.1', 'root', '', 'test', 'UTF-8');
$fields = array(
	'ofuser' => 1,
	'sender' => 'test0',
	'sendtime' => 'NOW()',
);
$filter = array(
	'sender' => array('LIKE', 'test%')
);
var_dump(StorageEdit('sms', $fields, $filter));
