<?php
// Copyright 2015 The Haohaoxiuche Team Authors. All right reserved.
// Use of this source that is governed by a Apache-style
// license that can be found in the LICENSE file.
//
// 技術版API編號066 汽修人之信息盒子-我的新數據條數
//
// @authors hjboss <hongjiangproject@yahoo.com> 2015-12-18#
// @version 1.0.0
// @package hhxc
if (!defined('HHXC')) die('Permission denied');

$result = array('code' => '101', 'number' => '0');

$condition = array(
	'schema' => 'hh_techuser',
	'fields' => array(
		'(msg1+msg2+msg3+msg4+msg5+msg6+msg7+msg8) AS msg',
	),
	'filter' => array(
		'id' => Assign($params['uid'], 0),
	),
);
$record = StorageFindOne($condition);
if (is_array($record) and empty($record) == FALSE) {
	$result['number'] = $record['msg'];
}

