<?php
// Copyright 2015 The Haohaoxiuche Team Authors. All right reserved.
// Use of this source that is governed by a Apache-style
// license that can be found in the LICENSE file.
//
// 技術版APP 頁面公共預處理
//
// @authors hjboss <hongjiangproject@yahoo.com> 2015-01-02 #
// @version 1.0.0
// @package hhxc
if (!defined('HHXC')) die('Permission denied');

define('API_ROOT',    dirname(dirname(dirname(dirname(__FILE__)))));
define('API_VERSION', basename(dirname(dirname(__FILE__))));
define('API_NAME',    str_replace(API_ROOT . DIRECTORY_SEPARATOR, '', dirname(dirname(dirname(__FILE__)))));
define('URL_MOBILE', 'http://www.haohaoxiuche.com/hhxc-api/mobile');
define('URL_API',    'http://www.haohaoxiuche.com/hhxc-api/' . API_NAME . '/' . API_VERSION . '/index.php');

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


