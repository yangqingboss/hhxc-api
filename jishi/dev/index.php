<?php
// Copyright 2015 The Haohaoxiuche Team Authors. All right reserved.
// Use of this source that is governed by a Apache-style
// license that can be found in the LICENSE file.
//
// 深圳市觀微科技有限公司 好好修車APP 技師版之網絡服務接口API
//
// @authors hjboss <hongjiangproject@yahoo.com> 2015-12-14#
// @version 1.0.0
// @package hhxc
define('HHXC',        TRUE);
define('API_ROOT',    dirname(dirname(dirname(__FILE__))));
define('API_VERSION', basename(dirname(__FILE__)));
define('API_NAME',    str_replace(API_ROOT . DIRECTORY_SEPARATOR, '', dirname(dirname(__FILE__))));

## 預加載全局配置文件
if (API_VERSION != 'dev') {
	require_once(API_ROOT . DIRECTORY_SEPARATOR . 'config.production.php');
	error_reporting(0);
} else if (empty($_REQUEST['development']) and empty($argv)) {
	require_once(API_ROOT . DIRECTORY_SEPARATOR . 'config.test.php');
	error_reporting(E_ALL ^ E_NOTICE);
} else {
	require_once(API_ROOT . DIRECTORY_SEPARATOR . 'config.development.php');
	error_reporting(E_ALL ^ E_NOTICE);
}
require_once(API_ROOT . DIRECTORY_SEPARATOR . 'common.php');

## 數據庫預連接處理
$mysql = mysqli_connect(DB_HOST, DB_USER, DB_PWD) or die('Could not connect ' . mysqli_error($mysql));
mysqli_select_db($mysql, DB_NAME) or die('Permission denied for the database ' . DB_NAME);
mysqli_query($mysql, 'SET NAMES ' . DB_CHARSET);

## 提交參數值和返回值
$params = count($argv) >= 2 ? json_decode($argv[1], TRUE) : json_decode(Assign($_REQUEST['data'], '{}'), TRUE);
$result = array(
	'code' => '100',         // 返回結果之碼值
	'msg'  => MESSAGE_ERROR, // 返回結果之消息
);

## 統計API調用信息
if (DEBUG == FALSE) {
	$api_log = array(
		'apicode'   => Assign($params['code']),
		'uid'       => Assign($params['uid'], '0'),
		'deviceid'  => Assign($params['deviceid']),
		'createdat' => 'NOW()',
		'ip'        => $_SERVER['REMOTE_ADDR'],
		'apiver'    => API_NAME . '-' . API_VERSION,
	);

	StorageAdd('hh_api_log', $api_log);
}

## 加載相對應API接口腳本
$script = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'api-' . substr(strval($params['code'] + 1000), 1, 3) . '.php';
if (file_exists($script) == FALSE) {
	die('Permission denied for the APIs');
} else {
	require_once($script);
}

mysqli_close($mysql);
die(JsonEncode($result));

/**************************************** 公共函數 ****************************************/
