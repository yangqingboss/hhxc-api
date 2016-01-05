<?php
// Copyright 2015 The Haohaoxiuche Team Authors. All right reserved.
// Use of this source that is governed by a Apache-style
// license that can be found in the LICENSE file.
//
// 技術版API編號113 獲取車型列表
//
// @authors hjboss <hongjiangproject@yahoo.com> 2015-12-18#
// @version 1.0.0
// @package hhxc
if (!defined('HHXC')) die('Permission denied');

$result = array('code' => '101', 'msg' => '', 'data' => array());
$condition = array(
	'schema' => 'car_type',
	'fields' => array('id', 'keyword1'),
	'filter' => array(
		'pid' => 0,
		'zhuangtai' => 1,
	),
	'others' => 'ORDER BY firstchar, id ASC',
);
$recordset = StorageFind($condition);
if (is_array($recordset) == FALSE or empty($recordset) == TRUE) {
	$result['msg'] = MESSAGE_EMPTY;
} else {
	foreach ($recordset as $index => $row) {
		$buffer = array(
			'carType'     => $row['id'],
			'carTypeName' => $row['keyword1'],
			'data'        => array(),
		);
		
		$condition_pid = array(
			'schema' => 'car_type',
			'fields' => array('id'),
			'filter' => array(
				'pid' => $row['id'],
				'zhuangtai' => 1,
			),
		);
		$condition_sub = array(
			'schema' => 'car_type',
			'fields' => array('id', 'keyword1'),
			'filter' => array(
				'pid' => array('IN', SQLSub($condition_pid)),
				'zhuangtai' => 1,
			),
		);
		$buf = StorageFind($condition_sub);
		if (is_array($buf) and empty($buf) == FALSE) {
			foreach ($buf as $number => $row_car) {
				$e = str_replace($row['keyword1'], '', $row_car['keyword1']);
				$buffer['data'][] = empty($e) ? $row_car['keyword1'] : $e;
			}
		}

		$result['data'][] = $buffer;
	}
}

