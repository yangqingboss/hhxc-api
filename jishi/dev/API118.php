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
	'schema' => 'hh_todaytop',
	'fields' => array('title', 'url'),
	'filter' => array(
		'zhuangtai' => 1,
	),
	'others' => 'ORDER BY site DESC',
);

$result = array('code' => '101', 'databasever' => Assign($params['databasever'], 0), 'data' => array());
$recordset = StorageFind($condition);
if (is_array($recordset) and empty($recordset) == FALSE) {
	foreach ($recordset as $index => $row) {
		$result['data'][] = array(
			'textname' => $row['title'],
			'url'      => $row['url'],
		);
	}
}

