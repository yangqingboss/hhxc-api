<?php
// Copyright 2015 The Haohaoxiuche Team Authors. All right reserved.
// Use of this source that is governed by a Apache-style
// license that can be found in the LICENSE file.
//
// 好好修車手機網頁版 初始化預處理腳本
//
// @authors hjboss <hongjiangproject@yahoo.com> 2015-12-12#
// @version 1.0.0
// @package hhxc
define('HHXC',        TRUE);
define('API_ROOT',    dirname(dirname(__FILE__)));
define('API_VERSION', '1.8.0');

require_once(API_ROOT . DIRECTORY_SEPARATOR . 'config.mobile.php');
require_once(API_ROOT . DIRECTORY_SEPARATOR . 'common.php');

## 數據庫預連接處理
$mysql = mysqli_connect(DB_HOST, DB_USER, DB_PWD) or die('Could not connect: ' . mysqli_error($mysql));
mysqli_query($mysql, 'SET NAMES ' . DB_CHARSET);

