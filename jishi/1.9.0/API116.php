<?php
// Copyright 2015 The Haohaoxiuche Team Authors. All right reserved.
// Use of this source that is governed by a Apache-style
// license that can be found in the LICENSE file.
//
// 技術版API編號116 獲取故障類別類別
//
// @authors hjboss <hongjiangproject@yahoo.com> 2015-12-18#
// @version 1.0.0
// @package hhxc
if (!defined('HHXC')) die('Permission denied');

$result = array('code' => '101', 'data' => array());

$condition = array(
	'schema' => 'car_symptom',
	'fields' => '*',
	'filter' => array(
		'type' => 7,
		'pid'  => 0,
	),
	'others' => 'ORDER BY sort ASC',
);
$recordset = StorageFind($condition);
if (is_array($recordset) == FALSE or empty($recordset) == TRUE) {
	$result['msg'] = MESSAGE_EMPTY;
} else {
	foreach ($recordset as $index => $row) {
		$buffer = array(
			'faultType' => $row['id'],
			'faultName' => $row['title'],
			'data'      => array(),
		);

		$condition_sub = array(
			'schema' => 'car_symptom',
			'fields' => '*',
			'filter' => array(
				'type' => 7,
				'pid'  => $row['id'],
			),
			'others' => 'ORDER BY id ASC',
		);
		$buf = StorageFind($condition_sub);
		if (is_array($buf) and empty($buf) == FALSE) {
			foreach ($buf as $number => $row_item) {
				$buffer['data'][] = $row_item['title'];
			}
		}

		$result['data'][] = $buffer;
	}
}

