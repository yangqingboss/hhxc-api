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
$script = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'API' . substr(strval($params['code'] + 1000), 1, 3) . '.php';
if (file_exists($script) == FALSE) {
	die('Permission denied for the APIs');
} else {
	require_once($script);
}

mysqli_close($mysql);
die(JsonEncode($result));

/**************************************** 公共函數 ****************************************/
// 檢測用戶OpenID有效性
function CheckOpenID($loginid, $uid = 0) {
	if (DEBUG == FALSE) {
		$result = StorageQueryOne('hh_techuser', '*',  array('loginid' => $loginid));

		if (is_array($result)) {
			return $uid > 0 and $uid == Assign($result['id'], '0');
		}

		return FALSE;
	}

	return TRUE;
}

## 設置技師用戶積分並且記錄積分日誌
## 若達到每日積分極限則返回-1 
## 若達到最終積分極限則返回-2
## 否則返回當前新增積分
function Techuser_setScore($id, $scoretype, $apicode = '') {
	$schema = 'hh_techuser'; $schema_s = 'hh_score';

	## 獲取當前級別積分限制
	$record = StorageFindID('hh_score_type', $scoretype);
	if (is_array($record) == FALSE or empty($record) == TRUE) {
		return FALSE;
	}

	$score    = intval($record['score']);
	$dayscore = intval($record['dayscore']);
	$maxscore = intval($record['maxscore']);

	## 獲取已使用積分次數
	$techuser   = Assign(StorageFindID($schema, $id), array());
	$dengji     = StorageFindOne($schema_s, '*', array('dengji' => intval($techuser['grade'])));
	$times_used = $dengji['chakan'] - $techuser['times_cx'];

	## 判斷每日積分限制
	if (($techuser["s{$scoretype}_day"] + $score) > $dayscore) {
		return -1;
	}

	## 判斷最終積分限制
	if (($techuser["s{$scoretype}"] + $score) > $maxscore) {
		return -2;
	}

	$fields = array(
		"s{$scoretype}"     => "s{$scoretype}+1",
		"s{$scoretype}_day" => "s{$scoretype}_day+1"
	);
	StorageEditByID($schema, $fields, $id);

	## 更新總積分
	$fields = array(
		'score' => '(s0+s1+s2+s3+s4+s5+s6+s7+s8+s9+s10+s11+s12+s13)',
	);
	StorageEditByID($schema, $fields, $id);

	## 更新積分等級
	$other_g = 'ORDER BY score DESC LIMIT 1';
	$other_n = 'ORDER BY score ASC  LIMIT 1';
	$fields = array(
		'grade'     => "(SELECT dengji FROM {$schema_s} WHERE score<={$schema}.score {$other_g}",
		'needscore' => "(SELECT score FROM {$schema_s} WHERE score>{$schema}.score {$other_n})-score)",
	);
	$num = StorageEditByID($schema, $fields, $id);
	if (empty($num) == FALSE) {
		$fields = array(
			'times_cx' => "(SELECT chakan FROM {$schema_s} WHERE dengji=grade)-{$time_used}",
		);
		StorageEditByID($schema, $fields, $id);
	}

	## 更新積分排名
	$percent = 0.0;
	$condition = array(
		'schema' => array($schema),
		'fields' => array(
			'COUNT(*) AS h_count',
			"(SELECT COUNT(*) FROM {$schema} WHERE score<=t0.score) AS h_self",
		),
		'filter' => array('t0.id' => $id),
	);
	$record = StorageFindOne($condition);
	if (is_array($record) and empty($record) == FALSE) {
		$percent = round($record['h_self']*100/$record['h_count'], 2);
	}
	StorageEditByID($schema, array('percent' => $percent), $id);

	## 添加積分日誌
	

	return $score;
}

