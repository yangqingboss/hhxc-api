<?php
// Copyright 2015 The Haohaoxiuche Team Authors. All right reserved.
// Use of this source that is governed by a Apache-style
// license that can be found in the LICENSE file.
//
// 深圳市觀微科技有限公司 好好修車APP 基礎性公共類庫
//
// @authors hjboss <hongjiangproject@yahoo.com> 2015-12-12#
// @version 1.0.0
// @package hhxc
if (!defined('HHXC')) die('Permission denied');

require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'SSDB.php');

/**************************************** 全局參數 ****************************************/
define('API_URL',             'http://www.haohaoxiuche.com/hhxc-api/%s/%s/index.php');
define('API_TEST_URL',        'http://192.168.16.180:8080/hhxc-api/%s/%s/index.php');
define('AES_KEY',             'B2A64F598D9E643A98E04A9DC70FD5C6');
define('SMS_APIKEY',          '78a9e2064e61fe308c33161e7a6a5dff');
define('SMS_APIURL',          'http://apis.baidu.com/kingtto_media/106sms/106sms?mobile={MOBILE}&content={CONTENT}');
define('SMS_BRANDS',          '好好修车');
define('SMS_ACTION_REGISTER', '您的注册验证码：');
define('SMS_ACTION_BANDINGS', '您的手机绑定验证码：');
define('SMS_ACTION_PASSWORD', '您的密码验证码：');
define('MESSAGE_SUCCESS',     '提交成功！');
define('MESSAGE_ERROR',       '提交失败！');
define('MESSAGE_WARNING',     '验证失败！');
define('MESSAGE_EMPTY',       '无数据！');
define('KEY_PHONE',           'phone_d'); ## 兼容舊版API所新添的未加密手機號碼
define('IMAGE_ROOT',          dirname(API_ROOT) . DIRECTORY_SEPARATOR . 'api' . DIRECTORY_SEPARATOR . 'userimg');

$GLOBALS['DB_KEYWORDS'] = array('NOW', 'SUM', 'DATE', 'COUNT', 'MIN', 'MAX', 'HOUR', 'MINUTE', 'MONTH', '(');
$GLOBALS['DB_SYMBOLS']  = array(
	'EQ' => '=', 'NEQ' => '<>', 'GT'   => '>',      'EGT'      => '>=', 
	'LT' => '<', 'ELT' => '<=', 'LIKE' => ' LIKE ', 'NOT LIKE' => ' NOT LIKE ', 
);

$mysql = NULL;
$ssdb  = NULL;
$ssdb_prefix = '';

/**************************************** 公共函數 ****************************************/
// 若參數是空值則返回給定的默認值
function Assign($object, $default = '') {
	return empty($object) ? $default : $object;
}

// 基於字符獲取子字符串
function SubString($str, $start = 0, $length = 1000000) {
	return mb_substr($str, $start, $length, 'UTF-8');
}

// 將字典數組編譯成JSON字符串
function JsonEncode($object = array()) {
	$buf = urldecode(json_encode(json_unicode($object)));
	$buf = str_replace("\r\n", "\\n", $buf);
	$buf = str_replace("\n",   "\\n", $buf);

	return $buf;
}

function json_unicode($object) {
	if (is_array($object) == FALSE) {
		$object = urlencode(str_replace('"', '\"', $object));
	} else {
		foreach ($object as $key => $val) {
			$object[urlencode($key)] = json_unicode($val);
		}
	}

	return $object;
}

// 基於GET方式發送HTTP請求
function HttpGet($url) {
	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);

	$res = curl_exec($ch);
	curl_close($ch);

	return $res;
}

// 基於POST方式發送HTTP請求
function HttpPost($url, $data = array()) {
	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

	$res = curl_exec($ch);
	curl_close($ch);

	return $res;
}

// 單位測試報告
function UnitTest($apicode, $request = array()) {
	$script = ''; $response = '';

	if (API_VERSION != 'dev' and API_VERSION != 'test') {
		$script = sprintf(API_URL, API_NAME, API_VERSION);
	} else if (empty($_REQUEST['development']) and count($argv) == 1) {
		$script = sprintf(API_URL, API_NAME, API_VERSION);
	} else if (count($argv) == 1) {
		$script = sprintf(API_TEST_URL, API_NAME, API_VERSION);
	} else {
		$script = join(array(API_ROOT, API_NAME, 'dev', 'index.php'), DIRECTORY_SEPARATOR);
	}
	
	$request['code'] = "{$apicode}";
	$params = JsonEncode($request);

	if (strpos($script, 'http') === 0) {
		$response = HttpPost($script, array('data' => $params));
	} else {
		$response = shell_exec(sprintf("/usr/bin/php %s '%s'", $script, $params));
	}

	echo "Request: ";
	print_r($request);
	echo "Response: ";
	$result = json_decode($response, TRUE);
	if ($result) {
		print_r($result);
	} else {
		print_r($response);
	}
}

// 短信發送網絡服務
function SMS($mobile, $message) {
	$url = str_replace('{MOBILE}', $mobile, SMS_APIURL);
	$url = str_replace('{CONTENT}', urlencode('【' . SMS_BRANDS . "】{$message}"), $url);
	$header = array(
		'apikey: ' . SMS_APIKEY,
	);

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_HTTPHEADER,     $header);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_URL,            $url);
	$res = curl_exec($ch);
	curl_close($ch);

	return strpos($res, 'Success') > -1 ? TRUE : FALSE;
}

// 生成給定長度隨機字符
function RandomChars($length = 4, $alpha = FALSE) {
	$chars = $alpha ? '1234567890qwertyuiopasdfghjklzxcvbnm' : '1234567890';

	$buf = array();
	for ($index = 0; $index < $length; $index++) {
		$random = rand(0, strlen($chars) - 1);
		$buf[] = substr($chars, $random, 1);
	}

	return join($buf, '');
}

// 基於給定字符集構建數據庫鏈接
function StorageConnect($host, $user, $pwd, $name, $charset) {
	global $mysql;

	if (empty($mysql) == TRUE) {
		$mysql = mysqli_connect($host, $user, $pwd) or die('Could not connect: ' . mysqli_error($mysql));
		mysqli_select_db($mysql, $name) or die('Permission denied for the database ' . $name);
		mysqli_query($mysql, 'SET NAMES ' . $charset);
	}

	return $mysql;
}

// 將給定的數據數組添加到給定的數據表並且返回當前ID
function StorageAdd($schema, $data = array(), $debug = FALSE) {
	global $mysql;

	## 數據類型不合理
	if (is_array($data) == FALSE) die('StorageAdd Error: Data valid for ' . var_export($data, TRUE));

	$fields = $values = array();
	foreach ($data as $key => $val) {
		if (is_null($val)) continue;

		$fields[] = "`{$key}`";
		$values[] = StorageExpress($val);
	}

	$sql = "INSERT INTO `{$schema}` (" . join($fields, ', ') . ') VALUES (' . join($values, ', ') . ')';
	if ($debug) die($sql);
	$res = mysqli_query($mysql, $sql);

	if ($res) {
		return mysqli_insert_id($mysql);
	}

	if (DEBUG) die(StorageError(__FUNCTION__, $sql));
	return FALSE;
}

// 基于给定的過濾条件在給定的數據表刪除相對應的數據記錄
function StorageDel($schema, $filter) {
	global $mysql;

	if (empty($filter)) die('StorageDel Error: Filter String Empty');

	$sql = "DELETE FROM `{$schema}` " . StorageWhere($filter);
	$res = mysqli_query($mysql, $sql);

	if ($res) {
		return mysqli_affected_rows($mysql);
	}

	if (DEBUG) die(StorageError(__FUNCTION__, $sql));
	return FALSE;
}

// 基於給定ID值在給定的數據表刪除特定的一條數據
function StorageDelByID($schema, $id) {
	global $mysql;

	if (is_numeric($id) == FALSE) die('StorageDelByID Error: ID Not Integer');

	$sql = "DELETE FROM `{$schema}` " . StorageWhere(array('id' => $id));
	$res = mysqli_query($mysql, $sql);

	if ($res) {
		return mysqli_affected_rows($mysql);
	}

	if (DEBUG) die(StorageError(__FUNCTION__, $sql));
	return FALSE;
}

// 基於給定的過濾條件在給定的數據表更新相對應的數據記錄
function StorageEdit($schema, $fields, $filter, $debug = FALSE) {
	global $mysql;

	if (is_array($fields) == FALSE) die('StorageEdit Error: Fields Not Array');
	if (empty($fields)) die('StorageEdit Error: Fields Empty Array');
	if (empty($filter)) die('StorageEdit Error: Filter String Empty');

	$values = array();
	foreach ($fields as $key => $val) {
		if (strpos($val, $key) === 0) {
			$values[] = "`{$key}`={$val}";
		} else {
			$values[] = "`{$key}`=" . StorageExpress($val);
		}
	}
	
	$sql = "UPDATE `{$schema}` SET " . join($values, ', ') . StorageWhere($filter);
	if ($debug) die($sql);
	$res = mysqli_query($mysql, $sql);

	if ($res) {
		return mysqli_affected_rows($mysql);
	}

	if (DEBUG) die(StorageError(__FUNCTION__, $sql));
	return FALSE;
}

// 基於給定ID值在給定的數據表更新特定的一條數據
function StorageEditByID($schema, $fields, $id) {
	global $mysql;

	if (is_array($fields) == FALSE) die('StorageEditByID Error: Field Not Array');
	if (empty($fields)) die('StorageEditByID Error: Fields Empty Array');
	if (empty($id)) die('StorageEditByID Error: ID Not Integer');

	$values = array();
	foreach ($fields as $key => $val) {
		if (strpos($val, $key) === 0) {
			$values[] = "`{$key}`={$val}";
		} else {
			$values[] = "`{$key}`=" . StorageExpress($val);
		}
	}
	$sql = "UPDATE `{$schema}` SET " . join($values, ',') . StorageWhere(array('id' => $id));
	$res = mysqli_query($mysql, $sql);

	if ($res) {
		return mysqli_affected_rows($mysql);
	}

	if (DEBUG) die(StorageError(__FUNCTION__, $sql));
	return FALSE;
}

// 基於給定的過濾條件查詢數據
function StorageFind($condition = array(), $debug = FALSE) {
	return StorageQuery(
		$condition['schema'], $condition['fields'], $condition['filter'], $condition['others'], $debug
	);
}

// 基於給定的過濾條件查詢第一條數據
function StorageFindOne($condition = array(), $debug = FALSE) {
	$condition['others'] = Assign($condition['others']) . ' LIMIT 1';
	$recordset = StorageFind($condition, $debug);

	if (is_array($recordset) and empty($recordset) == FALSE) {
		return $recordset[0];
	}

	return array();
}

// 基於給定ID值查詢特定的數據
function StorageFindID($schema, $id, $fields = '*', $debug = FALSE) {
	return StorageQueryByID($schema, $id, $fields, $debug);
}

// 基於給定的過濾條件查詢數據
function StorageQuery($schema, $fields = '*', $filter = '', $str = '', $debug = FALSE) {
	global $mysql;

	$sql = "SELECT" . StorageFields($fields) . "FROM " . StorageSchema($schema) . StorageWhere($filter);
	if (empty($str) == FALSE) $sql .= " {$str}";
	if ($debug == TRUE) die($sql);

	$res = mysqli_query($mysql, $sql);
	if ($res) {
		$data = array();
		
		while ($row = mysqli_fetch_array($res)) {
			foreach ($row as $key => $val) {
				if (is_string($val) == FALSE) {
					$row[$key] = $val;
				} else {
					$row[$key] = Charset($val, DB_CHARSET, CL_CHARSET);
				}
			}

			$data[] = $row;
		}

		return $data;
	}

	if (DEBUG) die(StorageError(__FUNCTION__, $sql));
	return FALSE;
}

// 基於給定的過濾條件分頁查詢數據
function StoragePage($schema, $fields = '*', $filter = '', $page = 1, $limit = 25, $str = '') {
	global $mysql;

	$sql = "SELECT" . StorageFields($fields) . "FROM " . StorageSchema($schema) . StorageWhere($filter);
	
	if (empty($page) or $page == 1) {
		$sql .= " LIMIT 0, {$limit}";
	} else if ($page >= 2) {
		$sql .= ' LIMIT ' . ($page - 1) * $limit . ', ' . $limit;
	}
	if (empty($str) == FALSE) $sql .= " {$str}";

	$res = mysqli_query($mysql, $sql);
	if ($res) {
		$data = array();

		while ($row = mysqli_fetch_array($res)) {
			foreach ($row as $key => $val) {
				if (is_string($val) == FALSE) {
					$row[$key] = $val;
				} else {
					$row[$key] = Charset($val, DB_CHARSET, CL_CHARSET);
				}
			}

			$data[] = $row;
		}

		return $data;
	}

	if (DEBUG) die(StorageError(__FUNCTION__, $sql));
	return FALSE;
}

// 基於給定ID值查詢特定的數據
function StorageQueryByID($schema, $id, $fields = '*', $debug = FALSE) {
	global $mysql;

	$sql = "SELECT" . StorageFields($fields) . "FROM " . StorageSchema($schema) . StorageWhere(array('id' => $id));
	if (empty($str) == FALSE) $sql .= " {$str}";
	if ($debug) die($sql);

	$res = mysqli_query($mysql, $sql);
	if ($res) {
		while ($row = mysqli_fetch_array($res)) {
			foreach ($row as $key => $val) {
				$row[$key] = Charset($val, DB_CHARSET, CL_CHARSET);
			}

			return $row;
		}

		return array();
	}

	if (DEBUG) die(StorageError(__FUNCTION__, $sql));
	return FALSE;
}

// 基於給定的過濾條件統計數據數量
function StorageCount($schema, $filter = '') {
	global $mysql;

	$sql = "SELECT COUNT(*) AS h_ct FROM " . StorageSchema($schema) . StorageWhere($filter);
	$res = mysqli_query($mysql, $sql);

	if ($res) {
		while ($row = mysqli_fetch_array($res)) {
			return intval($row['h_ct']);
		}

		return 0;
	}

	if (DEBUG) die(StorageError(__FUNCTION__, $sql));
	return FALSE;
}

// 基於給定的過濾條件統計數據數量
function StorageRows($condition = array()) {
	global $mysql;
	
	$sql = SQLSub($condition);
	$res = mysqli_query($mysql, $sql);

	if ($res) {
		return mysqli_num_rows($res);
	}

	if (DEBUG) die(StorageError(__FUNCTION__, $sql));
	return FALSE;
}

// 執行給定的SQL語句
function StorageSQL($sql) {
	global $mysql;

	$sql = strtoupper($sql);
	$res = mysqli_query($mysql, $sql);
	
	if ($res) {
		if (strpos($sql, "SELECT") !== 0) {
			return TRUE;
		}

		$data = array();
		while ($row = mysqli_fetch_array($res)) {
			foreach ($row as $key => $val) {
				$row[$key] = Charset($val, DB_CHARSET, CL_CHARSET);
			}

			$data[] = $row;
		}

		return $data;
	}

	if (DEBUG) die(StorageError(__FUNCTION__, $sql));
	return FALSE;
}

// 構造WHERE語句字符串
function StorageWhere($filter, $default = array()) {
	global $mysql;

	if (empty($filter)) return '';
	if (is_string($filter)) return vsprintf(" WHERE {$filter}", $default);

	if (is_array($filter) == FALSE) die('StorageWhere Error: Filter Invalid Array');
	if (empty($filter)) die('StorageWhere Error: Filter Array Empty');

	$sign = ' AND '; $columns = array();
	foreach ($filter as $key => $val) {
		if (strtoupper($key) == '@SIGN') {
			$sign = ' OR ';

		} else if (is_array($val) == FALSE) {
			$columns[] = "{$key}=" . StorageExpress($val);

		} else if (empty($val)) {
			continue;

		} else if (is_string($val[0])) {
			$columns[] = StorageWhereSimple($key, $val);

		} else if (is_array($val[0])) {
			$buf = array(); $buf_sign = ' AND ';
			foreach ($val as $buf_num => $buf_val) {
				if (is_string($buf_val)) {
					$buf_sign = strtoupper($buf_val);
					continue;
				}

				$buf[] = StorageWhereSimple($key, $buf_val);
			}
			$columns[] = '(' . join($buf, $buf_sign) . ')';
		}
	}

	return ' WHERE ' . join($columns, $sign);
}

function StorageWhereSimple($field, $args = array()) {
	$sign = strtoupper(Assign($args[0]));

	foreach ($GLOBALS['DB_SYMBOLS'] as $key => $val) {
		if ($sign == $key) {
			return "{$field}{$val}" . StorageExpress(Assign($args[1]));
		}
	}

	if (in_array($sign, array('BETWEEN', 'NOT BETWEEN')) == TRUE) {
		if (is_array($args[1]) == FALSE) die("StorageWhereSimple Error: {$sign} Not Array");

		$buf = array();
		for ($index = 0; $index < 2; $index++) {
			$buf[] = StorageExpress($args[$index]);
		}

		return "{$field} {$sign} " . join($buf, ' AND ');
	}

	if (in_array($sign, array('IN', 'NOT IN')) == TRUE) {
		if (is_string($args[1]) == TRUE) {
			return "{$field} {$sign} ({$args[1]})";
		}

		if (is_array($args[1]) == FALSE) die("StorageWhereSimple Error: {$sign} Not Array");

		$buf = array();
		foreach ($args[1] as $key => $val) {
			$buf[] = StorageExpress($val);
		}

		return "{$field} {$sign} (" . join($buf, ', ') . ')';
	}

	return '';
}

function StorageExpress($express) {
	global $mysql;

	foreach ($GLOBALS['DB_KEYWORDS'] as $word) {
		if (strpos($express, $word) === 0) {
			return $express;
		}
	}

	for ($index = 0; $index <= 9; $index++) {
		if (strpos($express, "t{$index}.") === 0) {
			return $express;
		}
	}

	$express = Charset($express, DB_CHARSET, CL_CHARSET);
	return "'" . mysqli_real_escape_string($mysql, $express) . "'";
}

function StorageSchema($schema) {
	if (is_string($schema) == FALSE and is_array($schema) == FALSE) {
		die('StorageSchema Error: Schema Not String or Array');
	}

	if (is_string($schema)) {
		if (strpos($schema, '(') === 0) {
			return $schema;
		}

		return "`{$schema}`";
	}

	$buffer = array();
	foreach ($schema as $number => $name) {
		$buffer[] = "`{$name}` AS t{$number}";
	}

	return join($buffer, ', ');
}

function StorageFields($fields = '*') {
	if (empty($fields) or $fields == '*') return ' * ';

	if (is_array($fields) == FALSE) die('StorageField Error: Fields Not String or Array');
	
	return ' ' . join($fields, ', ') . ' ';
}

// 返回MySQL錯誤信息
function StorageError($function, $sql) {
	global $mysql;
	
	$msg = "{$function} \"{$sql}\" Error";
	$err = mysqli_error($mysql);

	if (is_string($err)) {
		$msg .= ': ' . $err;
	}

	return $msg;
}

// 構建子查詢SQL語句
function SQLSub($condition) {
	$sql = "SELECT" . StorageFields($condition['fields']) . "FROM " . StorageSchema($condition['schema']);
	$sql .= StorageWhere($condition['filter']);
	if (empty($condition['others']) == FALSE) $sql .= " {$condition['others']}";

	return $sql;
}

// 鍵值數據庫鏈接
function KVStorageConnect($host, $port, $pwd, $name) {
	global $ssdb;
	global $ssdb_prefix;

	if (empty($ssdb) == TRUE) {
		try {
			$ssdb = new SimpleSSDB($host, $port);
			if (empty($pwd) == FALSE) {
				$ssdb->auth(SSDB_PWD);
			}
	
			$ssdb_prefix = $name . '_';
			return $ssdb;

		} catch (SSDBException $e) {
			if (DEBUG == TRUE) {
				die('KVStorageConnect Error: ' . $e->getMessage());
			}
		}

		return FALSE;
	}

	return $ssdb;
}

// 鍵值數據庫添加數據
function KVStorageSet($key, $data) {
	global $ssdb;
	global $ssdb_prefix;

	if (is_array($data) == FALSE) die('KVStorageAdd Error: Data Not Array');

	try {
		return $ssdb->multi_hset($ssdb_prefix . $key, $data);

	} catch (SSDBException $e) {
		if (DEBUG == TRUE) {
			die('KVStorageSet Error: ' . $e->getMessage());
		}
	}

	return FALSE;
}

// 鍵值數據庫獲取數據
function KVStorageGet($key) {
	global $ssdb;
	global $ssdb_prefix;

	try {
		return $ssdb->hgetall($ssdb_prefix . $key);

	} catch (SSDBException $e) {
		if (DEBUG == TRUE) {
			die('KVStorageGet Error: ' . $e->getMessage());
		}
	}

	return FALSE;
}

// 鍵值數據庫刪除數據
function KVStorageDel($key) {
	global $ssdb;
	global $ssdb_prefix;

	try {
		return $ssdb->hclear($ssdb_prefix . $key);

	} catch (SSDBException $e) {
		if (DEBUG == TRUE) {
			die('KVStorageDel Error: ' . $e->getMessage());
		}
	}

	return FALSE;
}

// 鍵值數據庫判斷存在
function KVStorageExists($key) {
	global $ssdb;
	global $ssdb_prefix;

	try {
		return $ssdb->hsize($ssdb_prefix . $key) != 0;

	} catch (SSDBException $e) {
		if (DEBUG == TRUE) {
			die('KVStorageExists Error: ' . $e->getMessage());
		}
	}

	return FALSE;
}

// 鍵值數據庫獲取區間數據
function KVStorageScan($key_start, $key_end, $limit = 128, $order = TRUE) {
	global $ssdb;
	global $ssdb_prefix;

	try {
		$recordset = array(); $keys = array();

		if ($order == FALSE) {
			$keys = $ssdb->rhlist($ssdb_prefix . $key_start, $ssdb_prefix . $key_end, $limit);

		} else {
			$keys = $ssdb->hlist($ssdb_prefix . $key_start, $ssdb_prefix . $key_end, $limit);
		}

		foreach ($keys as $key) {
			$recordset[] = $ssdb->hgetall($key);
		}

		return $recordset;

	} catch (SSDBException $e) {
		if (DEBUG == TRUE) {
			die('KVStorageScan Error: ' . $e->getMessage());
		}
	}

	return FALSE;
}

// 字符串編碼轉換
function Charset($str, $from, $to) {
	if ($from == 'LATIN1' or $from == 'GBK') {
		return mb_convert_encoding($str, $to, 'GBK');
	}

	if ($to == 'LATIN1' or $to == 'GBK') {
		return mb_convert_encoding($str, 'GBK', $from);
	}

	return mb_convert_encoding($str, $to, $from);
}

## 構建邀請碼字符串
function WithCode($username, $id, $others = '') {
	return substr(strtoupper(md5($username . $id . $others)), 0, 6);
}

## 將語音輸入全角字符轉換成半角字符
function SpeechWords($content) {
	$chars = array(
		'零', '一', '二', '三', '四', '五', '六', '七', '八', '九', 
		'零', '壹', '贰', '叁', '肆', '伍', '陆', '柒', '捌', '玖',
	);

	$alpha = array(
		'Ｑ' => 'Q', 'Ｗ' => 'W', 'Ｅ' => 'E', 'Ｒ' => 'R', 'Ｔ' => 'T', 'Ｙ' => 'Y',
		'Ｕ' => 'U', 'Ｉ' => 'I', 'Ｏ' => 'O', 'Ｐ' => 'P', 'Ａ' => 'A', 'Ｓ' => 'S', 
		'Ｄ' => 'D', 'Ｆ' => 'F', 'Ｇ' => 'G', 'Ｈ' => 'H', 'Ｊ' => 'J', 'Ｋ' => 'K', 
		'Ｌ' => 'L', 'Ｚ' => 'Z', 'Ｘ' => 'X', 'Ｃ' => 'C', 'Ｖ' => 'V', 'Ｂ' => 'B',
		'Ｎ' => 'N', 'Ｍ' => 'M',
		'ｑ' => 'q', 'ｗ' => 'w', 'ｅ' => 'e', 'ｒ' => 'r', 'ｔ' => 't', 'ｙ' => 'y', 
		'ｕ' => 'u', 'ｉ' => 'i', 'ｏ' => 'o', 'ｐ' => 'p', 'ａ' => 'a', 'ｓ' => 's', 
		'ｄ' => 'd', 'ｆ' => 'f', 'ｇ' => 'g', 'ｈ' => 'h', 'ｊ' => 'j', 'ｋ' => 'k', 
		'ｌ' => 'l', 'ｚ' => 'z', 'ｘ' => 'x', 'ｃ' => 'c', 'ｖ' => 'v', 'ｂ' => 'b',
		'ｎ' => 'n', 'ｍ' => 'm',
	);

	$content = trim($content);

	foreach ($chars as $number => $char) {
		$num = $number % 10;
		$content = str_replace($char, $num, $content);
	}

	foreach ($alpha as $upper => $lower) {
		$content = str_replace($upper, $lower, $content);
	}

	return trim($content, '.。,，');
}

function escape($str) {
	$ret = array();
	$buf = Charset($str, CL_CHARSET, DB_CHARSET);

	for ($index = 0; $index < strlen($buf); $index++) {
		if (ord($buf[$index]) < 127) {
			$ret[] = '%' . dechex(ord($buf[$index]));

		} else {
			$tmp = bin2hex(Charset(substr($buf, $index, 2), DB_CHARSET, 'ucs-2'));
			$ret[] = '%u' . $tmp;
			$index++;
		}
	}

	return join($ret, '');
}

function gethttp($url, $params = '') {
	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_POST, TRUE);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $params);

	$ret = curl_exec($ch);
	curl_close($ch);

	return $ret;
}

function fmtstr($str) {
	$buf = str_replace("\"", "\\\"", $str);
	$buf = str_replace("\t", "\\\t", $buf);
	return $buf;
}
