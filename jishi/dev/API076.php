<?php
// Copyright 2015 The Haohaoxiuche Team Authors. All right reserved.
// Use of this source that is governed by a Apache-style
// license that can be found in the LICENSE file.
//
// 技術版API編號076 總數量條數
//
// @authors hjboss <hongjiangproject@yahoo.com> 2015-12-18#
// @version 1.0.0
// @package hhxc
if (!defined('HHXC')) die('Permission denied');

$condition = array(
	'schema' => 'hh_dbver',
	'fields' => array('ver'),
	'filter' => array(
		'vername' => 'datatotal',
	),
);

$count = StorageCount($condition['schema'], $condition['filter']);
if (empty($count) == TRUE) {
	$result['msg'] = MESSAGE_EMPTY;
} else {
	$result = array('code' => '101', 'totalnumber' => 0);

	$record = StorageFindOne($condition);
	if (is_array($record) and empty($record) == FALSE) {
		$condition_sub = array(
			'schema' => 'hh_datatotal',
			'fields' => array(
				'(symptomp+anli+fault+faultcode+timing+(SELECT COUNT(*) FROM hh_techuser)) AS h_ct',
			),
			'others' => 'ORDER BY id DESC',
		);

		$buf = StorageFindOne($condition_sub);
		if (is_array($buf) and empty($buf) == FALSE) {
			$result['totalnumber'] = $buf['h_ct'];
		}
	}
}

