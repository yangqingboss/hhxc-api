<?php
// Copyright 2015 The Haohaoxiuche Team Authors. All right reserved.
// Use of this source that is governed by a Apache-style
// license that can be found in the LICENSE file.
//
// 技術版API編號022 查現象之獲取故障點列表
//
// @authors hjboss <hongjiangproject@yahoo.com> 2015-12-17#
// @version 1.0.0
// @package hhxc
if (!defined('HHXC')) die('Permission denied');

## 構建查詢故障點SQL
$condition_sub = array(
	'schema' => 'car_symptom_fault',
	'fields' => array('offault'),
	'filter' => array(
		'ofsymptom' => Assign($params['pheid'], 0),
	),
);
$condition = array(
	'schema' => 'car_fault',
	'fields' => array('id'),
	'filter' => array(
		'type' => array('NEQ', 7),
		'id'   => array('IN', SQLSub($condition_sub))
	),
	'others' => 'LIMIT 20',
);

$recordset = StorageFind($condition);
if (is_array($recordset) == FALSE or empty($recordset) == TRUE) {
	$result['msg'] = MESSAGE_EMPTY;
} else {
	$result = array('code' => '101', 'data' => array());

	foreach ($recordset as $index => $row) {
		$result['data'][] = $row['id'];
	}

	if (CheckOpenID($params['openid'], $params['uid']) == TRUE) {
		Techuser_setScore($params['uid'], 2);
	}
}
