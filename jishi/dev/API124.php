<?php
// Copyright 2015 The Haohaoxiuche Team Authors. All right reserved.
// Use of this source that is governed by a Apache-style
// license that can be found in the LICENSE file.
//
// 技術版API編號118 今日頭條數據
//
// @authors hjboss <hongjiangproject@yahoo.com> 2015-12-18#
// @version 1.0.0
// @package hhxc
if (!defined('HHXC')) die('Permission denied');

$condition = array(
	'schema' => 'car_odbfault',
	'fields' => array('obdcode'),
	'filter' => array(
		'obdcode' => array('LIKE', "{$params['codetype']}%"),
	),
);
$recordset = StorageFind($condition);
if (is_array($recordset) == FALSE or empty($recordset) == TRUE) {
	$result['msg'] = MESSAGE_EMPTY;
} else {
	$result = array('code' => '101', 'databasever' => time(), 'data' => array());
	$buffer = $firstlist = array();

	foreach ($recordset as $index => $row) {
		$first = strtoupper(substr(Assign($row['obdcode']), 0, 4));
			
		if (empty($first)) {
			continue;
		}

		if (in_array($first, $firstlist) == FALSE) {
			$firstlist[] = $first;

			$buffer[$first] = array(
				'index'       => $first,
				'codedatalit' => array(
					strtoupper($row['obdcode']),
				),
			);

			continue;
		}

		$info = strtoupper($row['obdcode']);
		if (in_array($info, $buffer[$first]['codedatalit']) == FALSE) {
			$buffer[$first]['codedatalit'][] = $info;
		}
	}
		
	sort($firstlist);
	foreach ($firstlist as $index => $alpha) {
		$result['data'][] = $buffer[$alpha];
	}
}

