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

$schema = 'odbfault-ANDROID_';
$buffer = KVStorageScan($schema . Assign($params['databasever'], 0), $schema . time(), 1, TRUE);
if (empty($buffer) == FALSE) {
	//$result = Assign(unserialize($buffer[0]['cache']));
}

if (empty($result['data']) == TRUE) {
	$condition = array(
		'schema' => 'car_odbfault',
		'fields' => array('obdcode', 'vwcode'),
	);

	$recordset = StorageFind($condition);
	if (is_array($recordset) and empty($recordset) == FALSE) {
		$result = array('code' => '101', 'databasever' => time(), 'data' => array());
		$buffer = $firstlist = array();

		foreach ($recordset as $index => $row) {
			$first = strtoupper(substr(Assign($row['obdcode']), 0, 1));
			$dcode = strtoupper(substr(Assign($row['vwcode']), 0, 1));
			
			if (empty($first)) {
				continue;
			}

			## 處理大眾故障碼
			if (strlen($dcode) > 0) {
				if (in_array($dcode, $firstlist) == FALSE) {
					$firstlist[] = $dcode;
	
					$buffer[$dcode] = array(
						'index'    => $dcode,
						'codetype' => array(
							strtoupper(substr($row['vwcode'], 0, 3)),
						),
					);
				} else {
					$info = strtoupper(substr($row['vwcode'], 0, 3));
					if (in_array($info, $buffer[$dcode]['codetype']) == FALSE) {
						$buffer[$dcode]['codetype'][] = $info;
					}
				}
			}

			## 處理普通故障碼
			if (in_array($first, $firstlist) == FALSE) {
				$firstlist[] = $first;

				$buffer[$first] = array(
					'index'    => $first,
					'codetype' => array(
						strtoupper(substr($row['obdcode'], 0, 3)),
					),
				);

				continue;
			}

			$info = strtoupper(substr($row['obdcode'], 0, 3));
			if (in_array($info, $buffer[$first]['codetype']) == FALSE) {
				$buffer[$first]['codetype'][] = $info;
			}
		}

		foreach ($buffer as $key => $item) {
			sort($buffer[$key]['codetype']);
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

		//KVStorageSet($schema . $result['databasever'], array('cache' => serialize($result)));
	}
}

