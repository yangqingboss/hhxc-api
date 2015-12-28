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

## 頁面URL設置
define('PAGE_ANLI',     'http://www.haohaoxiuche.com/html_hhxc_anli.php?uid=%d&openid=%s&debug=%d&resultid=');
define('PAGE_ZHENGSHI', 'http://www.haohaoxiuche.com/api_zhengshi.php?uid=%d&openid=%s&theid=');
define('ICON_DEFAULT',  'http://haohaoxiuche.com/css/icon_default.png');
define('ICON_PATH',     'http://haohaoxiuche.com/api/userimg/');
define('PIC_I_PATH',    join(array(dirname(API_ROOT), 'api', 'userimg'), DIRECTORY_SEPARATOR));
define('PIC_F_PATH',    join(array(dirname(API_ROOT), 'api', 'forumimg'), DIRECTORY_SEPARATOR));
define('PIC_Q_PATH',    join(array(dirname(API_ROOT), 'api', 'qzhilistimg'), DIRECTORY_SEPARATOR));
define('PIC_L_PATH',    join(array(dirname(API_ROOT), 'api', 'forumlistimg'), DIRECTORY_SEPARATOR));
define('PIC_Z_PATH',    join(array(dirname(API_ROOT), 'api', 'zhaopinlistimg'), DIRECTORY_SEPARATOR));
define('NICK_DEFAULT',  '汽修人');

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
KVStorageConnect(SSDB_HOST, SSDB_PORT, SSDB_PWD, SSDB_NAME);

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
		$result = StorageQueryOne('hh_techuser', '*',  array('loginid' => $loginid, 'zhuangtai' => 1));

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
function Techuser_setScore($id, $scoretype) {
	global $params;
	$schema = 'hh_techuser'; $schema_s = 'hh_score'; $schema_log = 'hh_score_log';

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
	$dengji     = StorageFindOne(array('schema' => $schema_s, 'filter' => array('dengji' => $techuser['grade'])));
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
		"s{$scoretype}"     => "s{$scoretype}+{$score}",
		"s{$scoretype}_day" => "s{$scoretype}_day+{$score}"
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
		'grade'     => "(SELECT dengji FROM {$schema_s} WHERE score<={$schema}.score {$other_g})",
		'needscore' => "((SELECT score FROM {$schema_s} WHERE score>{$schema}.score {$other_n})-score)",
	);
	$num = StorageEditByID($schema, $fields, $id);
	if (empty($num) == FALSE) {
		$fields = array(
			'times_cx' => "(SELECT chakan FROM {$schema_s} WHERE dengji=grade)-{$times_used}",
		);
		StorageEditByID($schema, $fields, $id);
	}

	## 更新積分排名
	$percent = 0.0;
	$condition = array(
		'schema' => array($schema),
		'fields' => array(
			"(SELECT COUNT(*) AS h_count FROM {$schema}) AS h_count",
			"(SELECT COUNT(*) FROM {$schema} WHERE score<=t0.score) AS h_self",
		),
		'filter' => array('t0.id' => $id),
	);
	$buf = StorageFindOne($condition);
	if (is_array($buf) and empty($buf) == FALSE) {
		$percent = round($buf['h_self']*100/$buf['h_count'], 2);
	}
	StorageEditByID($schema, array('percent' => $percent), $id);

	## 添加積分日誌
	$score_log = array(
		'uid'       => $id,
		'createdat' => date('Y-m-d H:i:s'),
		'scoretype' => $scoretype,
		'score'     => $score,
		'apicode'   => Assign($params['code'], 0),
		'oldscore'  => $techuser['score'],
	);
	$score_log_key = "score_{$id}_" . time();
	KVStorageSet($score_log_key, $score_log);
	//StorageAdd($schema_log, $log);

	## 兼容可兌換積分
	if (empty($techuser['rankinit']) == FALSE) {
		$fields = array(
			'rankscore' => 'rankscore+1',
		);
		StorageEditByID($schema, $fields, $techuser['id']);

		## 添加可兌換積分積分
		$rankscore_log = array(
			'uid'       => $id,
			'createdat' => date('Y-m-d H:i:s'),
			'scoretype' => $scoretype,
			'score'     => $score,
			'apicode'   => Assign($params['code'], 0),
			'oldscore'  => $techuser['rankscore'],
		);
		$rankscore_log_key = "rankscore_{$id}_" . time();
		KVStorageSet($rankscore_log_key, $rankscore_log);
	}

	return $score;
}

## 可兌換節分初始化
function Techuser_rankinit($uid) {
	$schema = 'hh_techuser';
	$record = StorageFindID($schema, $uid);
	if (is_array($record) == FALSE or empty($record) == TRUE) {
		return FALSE;
	}

	if (empty($record['rankinit']) == TRUE) {
		$score = $record['score'];

		## 按照法則將累計積分轉換成可兌換積分

		## 可兌換積分激活
		$fields = array(
			'rankinit'  => 1,
			'rankscore' => $score,
		);
		StorageEditByID($schema, $fields, $uid);
	}

	return TRUE;
}

## 設置技師用戶經驗並且記錄經驗日誌
function Techuser_setRank($id, $ranktype) {
}

## 記錄用戶搜索記錄
function Techuser_search($uid, $content, $type, $word_id = array(), $count = 0) {
	if (is_array($word_id)) $word_id = join($word_id, ',');

	$data = array(
		'ofuser'      => $uid,
		'content'     => $content,
		'createdat'   => 'NOW()',
		'type'        => $type,
		'wordid'      => $word_id,
		'resultcount' => $count,
	);

	$id = StorageAdd('hh_techuser_search', $data);
	if (empty($id) == FALSE) {
		return $id;
	}

	return FALSE;
}

function httpURL($url, $text, $numeric = FALSE) {
	$domain = sprintf('http://www.goviewtech.com:%d/bnc_search2_ctrl.ben?', DEBUG ? 3000 : 3000);
	$text   = $numeric ? $text : escape($text);
	return Assign(gethttp($domain . $url . $text), '0');
}

function GetWord($text) {
	$p = httpURL('cmd1=getword&cmd2=pinpai&cmd3=', $text);
	$k = httpURL('cmd1=getword&cmd2=keyword&cmd3=', $text);
	return "{$p};{$k}";
}

function GetPinpai($text) {
	return httpURL('cmd1=getword&cmd2=pinpai&cmd3=', $text);
}

function GetFault($text) {
	return httpURL('cmd1=getword&cmd2=fault&cmd3=', $text);
}

function GetKeyword($text) {
	return httpURL('cmd1=getword&cmd2=keyword&cmd3=', $text);
}

function GetAnliKeyword($text) {
	return httpURL('cmd1=getword&cmd2=anlikeyword&cmd3=', $text);
}

function GetAnli($text) {
	return httpURL('cmd1=getword&cmd2=getanli&cmd3=', $text, TRUE);
}

function GetAnliList($text) {
	return httpURL('cmd1=getword&cmd2=getanli_list&cmd3=', $text, TRUE);
}

function CheckTimes($openid, $uid) {
	$condition = array(
		'schema' => 'hh_techuser',
		'fields' => array('times_cx'),
		'filter' => array(
			'loginid' => $openid,
			'id'      => $uid,
		),
	);

	$record = StorageFindOne($condition);
	if (is_array($record) and empty($record) == FALSE) {
		return $record['times_cx'];
	}

	return FALSE;
}

function SetTimes($openid, $uid) {
	$fields = array('times_cx' => 'times_cx+1');
	$filter = array('loginid' => $params['openid'], 'id' => $params['id']);
	return StorageEdit('hh_techuser', $fields, $filter);
}

function RefreshMsg($uid) {
	if(empty($uid)) return;

	$messages = array(
		'msg1' => "(SELECT COUNT(*) FROM hh_techforum WHERE pubuser='{$uid}' AND isnewmsg=1 AND type=1)",
		'msg2' => "(SELECT COUNT(*) FROM hh_techforum WHERE pubuser='{$uid}' AND isnewmsg=1 AND type=2)",
		'msg3' => "(SELECT COUNT(*) FROM hh_techqzhi  WHERE pubuser='{$uid}' AND isnewmsg=1)",
		'msg4' => "(SELECT COUNT(*) FROM hh_techforum_list WHERE at='{$uid}' AND isnewat=1  AND type=1)",
		'msg5' => "(SELECT COUNT(*) FROM hh_techforum_list WHERE at='{$uid}' AND isnewat=1  AND type=2)",
		'msg6' => "(SELECT COUNT(*) FROM hh_techqzhi_list  WHERE at='{$uid}' AND isnewat=1)",
		'msg7' => "(SELECT COUNT(*) FROM hh_zhaopin   WHERE  ofuser='{$uid}' AND isnewmsg=1)",
		'msg8' => "(SELECT COUNT(*) FROM hh_zhaopin_list  WHERE  at='{$uid}' AND isnewat=1)",
	);

	foreach ($messages as $key => $sql) {
		$fields = array(
			$key => $sql,
		);
		StorageEditByID('hh_techuser', $fields, $uid);
	}
}

