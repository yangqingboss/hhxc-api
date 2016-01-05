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
	'fields' => array('obdcode', 'vwcode'),
	'filter' => array(
		'obdcode' => array('LIKE', "{$params['codetype']}%"),
		'vwcode'  => array('LIKE', "{$params['codetype']}%"),
		'@SIGN'   => 'OR',
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
		$dcode = strtoupper(substr(Assign($row['vwcode']), 0, 4));

		if (empty($first)) {
			continue;
		}

		## 處理大眾故障碼
		if (strpos($row['vwcode'], $params['codetype']) === 0) {
			if (in_array($dcode, $firstlist) == FALSE) {
				$firstlist[] = $dcode;
	
				$buffer[$dcode] = array(
					'index'       => $dcode,
					'codedatalit' => array(
						strtoupper(substr($row['vwcode'], 0, 5)),
					),
				);
			} else {
				$info = strtoupper(substr($row['vwcode'], 0, 5));
				if (in_array($info, $buffer[$dcode]['codetype']) == FALSE) {
					$buffer[$dcode]['codedatalit'][] = $info;
				}
			}

			continue;
		}

		# 處理普通故障碼
		if (in_array($first, $firstlist) == FALSE) {
			$firstlist[] = $first;

			$buffer[$first] = array(
				'index'       => $first,
				'codedatalit' => array(
					strtoupper(substr($row['obdcode'], 0, 5)),
				),
			);

			continue;
		}

		$info = strtoupper(substr($row['obdcode'], 0, 5));
		if (in_array($info, $buffer[$first]['codedatalit']) == FALSE) {
			$buffer[$first]['codedatalit'][] = $info;
		}
	}
	
	foreach ($buffer as $key => $item) {
		sort($buffer[$key]['codedatalit']);
	}

	sort($firstlist);
	foreach ($firstlist as $number => $item) {
		if (is_numeric($item)) {
			unset($firstlist[$number]);
			$firstlist[] = $item;
		}
	}

	foreach ($firstlist as $index => $alpha) {
		$result['data'][] = $buffer[$alpha];
	}
}

