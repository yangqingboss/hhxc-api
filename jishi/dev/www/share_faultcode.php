<?php
// Copyright 2015 The Haohaoxiuche Team Authors. All right reserved.
// Use of this source that is governed by a Apache-style
// license that can be found in the LICENSE file.
//
// 技術版APP 分享頁面公共頁面
//
// @authors hjboss <hongjiangproject@yahoo.com> 2015-12-14#
// @version 1.0.0
// @package hhxc
define('HHXC', TRUE);
require_once('common.php');
$faultcode = StorageFindID('car_odbfault', Assign($_REQUEST['id']));

$title = $faultcode['miaoshu'];
$content = $faultcode['fangan'];
include_once('share_common.php');
