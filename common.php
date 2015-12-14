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

$GLOBALS['DB_KEYWORDS'] = array('NOW', 'SUM', 'DATE', 'COUNT', 'MIN', 'MAX', 'HOUR', 'MINUTE', 'MONTH', '(');

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
	$buf = is_array($object) ? $object : array();

	foreach ($buf as $key => $val) {
		$buf[urlencode($key)] = urlencode(str_replace('"', '\"', $val));
	}

	$res = urldecode(json_encode($buf));
	$res = str_replace("\r\n", "\\n", $res);
	$res = str_replace("\n",   "\\n", $res);

	return $res;
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
		$response = exec(sprintf("/usr/bin/php %s '%s'", $script, $params));
	}

	echo "Request: ";
	print_r($request);
	echo "Response: ";
	$result = json_decode($response, TRUE);
	if ($result) {
		print_r($result);
	} else {
		echo "JSON ERROR: " . json_last_error();
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

	return strpos($res, 'Success') > -1 ? true : false;
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
	$mysql = mysqli_connect($host, $user, $pwd) or die('Could not connect: ' . mysqli_error($mysql));
	mysqli_select_db($mysql, $name) or die('Permission denied for the database ' . $name);
	mysqli_query($mysql, 'SET NAMES ' . $charset);

	return $mysql;
}

// 將給定的數據數組添加到給定的數據表並且返回當前ID
function StorageAdd($schema, $data = array()) {
	global $mysql;

	## 數據類型不合理
	if (is_array($data) == FALSE) die('StorageAdd Error: Data valid for ' . var_export($data, TRUE));

	$fields = $values = array();
	foreach ($data as $key => $val) {
		$fields[] = "`{$key}`";

		$invalid = TRUE;
		foreach ($GLOBALS['DB_KEYWORDS'] as $word) {
			if (strpos($val, $word) === 0) {
				$values[] = $val;
				$invalid = FALSE;
				break;
			}
		}

		if ($invalid) {
			$buf = Charset($val, CL_CHARSET, DB_CHARSET);
			$values[] = "'" . mysqli_real_escape_string($mysql, $buf) . "'";
		}
	}

	$sql = "INSERT INTO `{$schema}` (" . join($fields, ',') . ') VALUES (' . join($values, ',') . ')';
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
function StorageEdit($schema, $fields, $filter) {
	global $mysql;

	if (is_array($fields) == FALSE) die('StorageEdit Error: Field Not Array');
	if (empty($fields)) die('StorageEdit Error: Fields Empty Array');
	if (empty($filter)) die('StorageEdit Error: Filter String Empty');

	$values = array();
	foreach ($fields as $key => $val) {
		$invalid = TRUE;

		if (strpos($val, $key) === 0) {
			$values[] = $val;
			continue;
		}

		foreach ($DB_KEYWORDS as $word) {
			if (strpos($val, $word) === 0) {
				$values[] = $val;
				$invalid = FALSE;
				break;
			}
		}

		if ($invalid) {
			$buf = Charset($val, CL_CHARSET, DB_CHARSET);
			$values[] = "`{$key}`='" . mysqli_real_escape_string($mysql, $val) . "'";
		}
	}
	$sql = "UPDATE `{$schema}` SET " . join($values, ',') . StorageWhere($filter);
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
		$values[] = "`{$key}`='" . mysqli_real_escape_string($mysql, $val) . "'";
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
function StorageQuery($schema, $fields = '*', $filter = '', $str = '') {
	global $mysql;

	$sql = "SELECT" . StorageFields($fields) . "FROM " . StorageSchema($schema) . StorageWhere($filter);
	if (empty($str) == FALSE) $sql .= " {$str}";

	$res = mysqli_query($mysql, $sql);
	if ($res) {
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
				$row[$key] = Charset($val, DB_CHARSET, CL_CHARSET);
			}

			$data[] = $row;
		}

		return $data;
	}

	if (DEBUG) die(StorageError(__FUNCTION__, $sql));
	return FALSE;
}

// 基於給定ID值查詢特定的數據
function StorageQueryByID($schema, $id, $fields = '*') {
	global $mysql;

	$sql = "SELECT" . StorageFields($fields) . "FROM " . StorageSchema($schema) . StorageWhere(array('id' => $id));
	if (empty($str) == FALSE) $sql .= " {$str}";

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

	$sql = "SELECT COUNT(*) AS ct FROM " . StorageSchema($schema) . StorageWhere($filter);
	$res = mysqli_query($mysql, $sql);

	if ($res) {
		while ($row = mysqli_fetch_array($res)) {
			return intval($row['ct']);
		}

		return 0;
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
	foreach ($filter as $key => $value) {
		if (strtoupper($key) == '@SIGN') {
			$sign = ' OR ';

		} else if (is_array($value) == FALSE) {
			$buf = Charset($value, CL_CHARSET, DB_CHARSET);
			$columns[] = sprintf("`{$key}`='%s'", mysqli_real_escape_string($mysql, $buf));

		} else if (empty($value)) {
			continue;

		} else if (is_string($value[0])) {
			$columns[] = StorageWhereSimple($key, $value);

		} else if (is_array($value[0])) {
			$buffer = array(); $buffer_sign = ' AND ';
			foreach ($value as $buffer_num => $buffer_val) {
				if (is_string($buffer_val)) {
					$buffer_sign = strtoupper($buffer_val);
					continue;
				}

				$buffer[] = StorageWhereSimple($key, $buffer_val);
			}
			$columns[] = '(' . join($buffer, $buffer_sign) . ')';
		}
	}

	return ' WHERE ' . join($columns, $sign);
}

function StorageWhereSimple($key, $args = array()) {
	global $mysql;

	$str = "`{$key}`";
	$buf = Charset($args[1], CL_CHARSET, DB_CHARSET);

	switch (strtoupper($args[0])) {
	case 'EQ':
		$str .= "='" . mysqli_real_escape_string($mysql, $buf) . "'";
		break;

	case 'NEQ':
		$str .= "<>'" . mysqli_real_escape_string($mysql, $buf) . "'";
		break;

	case 'GT':
		$str .= ">'" . mysqli_real_escape_string($mysql, $buf) . "'";
		break;

	case 'EGT':
		$str .= ">='" . mysqli_real_escape_string($mysql, $buf) . "'";
		break;

	case 'LT':
		$str .= "<'" . mysqli_real_escape_string($mysql, $buf) . "'";
		break;

	case 'ELT':
		$str .= "<='" . mysqli_real_escape_string($mysql, $buf) . "'";
		break;

	case 'LIKE':
		$str .= " LIKE '" . mysqli_real_escape_string($mysql, $buf) . "'";
		break;

	case 'NOT LIKE':
		$str .= " NOT LIKE '" . mysqli_real_escape_string($mysql, $buf) . "'";
		break;

	case 'BETWEEN':
		if (is_array($args[1]) == FALSE) die('StorageWhereSimple Error: BETWEEN Not Array');

		$buffer = array();
		foreach ($args[1] as $num => $val) {
			$buffer[] = vsprintf("'%s'", mysqli_real_escape_string($mysql, (string)$val));
		}

		$str .= ' BETWEEN ' . join($buffer, ' AND ');
		break;

	case 'NOT BETWEEN':
		if (is_array($args[1]) == FALSE) die('StorageWhereSimple Error: NOT BETWEEN Not Array');

		$buffer = array();
		foreach ($args[1] as $num => $val) {
			$buffer[] = vsprintf("'%s'", mysqli_real_escape_string($mysql, (string)$val));
		}

		$str .= ' NOT BETWEEN ' . join($buffer, ' AND ');
		break;

	case 'IN':
		if (is_array($args[1]) == FALSE) die('StorageWhereSimple Error: NOT BETWEEN Not Array');

		$buffer = array();
		foreach ($args[1] as $num => $val) {
			$buffer[] = vsprintf("'%s'", mysqli_real_escape_string($mysql, (string)$val));
		}

		$str .= ' IN (' . join($buffer, ', ') . ')';
		break;

	case 'NOT IN':
		if (is_array($args[1]) == FALSE) die('StorageWhereSimple Error: NOT BETWEEN Not Array');

		$buffer = array();
		foreach ($args[1] as $num => $val) {
			$buffer[] = vsprintf("'%s'", mysqli_real_escape_string($mysql, (string)$val));
		}

		$str .= ' NOT IN (' . join($buffer, ', ') . ')';
		break;
	}

	return $str;
}

function StorageSchema($schema) {
	if (is_string($schema) == FALSE and is_array($schema) == FALSE) {
		die('StorageSchema Error: Schema Not String or Array');
	}

	if (is_string($schema)) return "`{$schema}`";

	$buffer = array();
	foreach ($schema as $number => $name) {
		$buffer[] = "`{$name}` AS t{$number}";
	}

	return join($buffer, ', ');
}

function StorageFields($fields = '*') {
	if (empty($fields) or $fields = '*') return ' * ';

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

