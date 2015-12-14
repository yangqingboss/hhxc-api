#!/usr/bin/php
<?php
// Copyright 2015 The Haohaoxiuche Team Authors. All right reserved.
// Use of this source that is governed by a Apache-style
// license that can be found in the LICENSE file.
//
// 深圳市觀微科技有限公司 好好修車APP 技師版之單位測試
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

## 獲取測試參數
$apicode = substr(strval(Assign($argv[1], '0') + 1000), 1, 3);
$samples = Assign($argv[2], '0');
$apifile = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'samples' . DIRECTORY_SEPARATOR . $apicode . '.php';

## 執行測試樣品
if (file_exists($apifile) == FALSE) {
	die("API '{$apicode}' NOT EXISTS");
} else {
	require_once($apifile);

	if (is_array($GLOBALS['DATA']) == FALSE) {
		die("Unit Test Error: '{$apicode}' Samples Not Array");
	}

	if ($samples !== '0') {
		$GLOBALS['DATA'] = array(
			($samples - 1) => Assign($GLOBALS['DATA'][$sample - 1], array()),
		);
	}

	echo '------------------------------ ';
	echo strtoupper(API_NAME) . ' API for ' . $apicode;
	echo ' ------------------------------';

	foreach ($GLOBALS['DATA'] as $number => $sample) {
		echo "\nUNIT TEST " . ($number + 1) . "th\n";
		UnitTest($apicode, $sample);
	}
}
