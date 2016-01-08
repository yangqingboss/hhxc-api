<?php
// Copyright 2015 The Haohaoxiuche Team Authors. All right reserved.
// Use of this source that is governed by a Apache-style
// license that can be found in the LICENSE file.
//
// 技術版API編號067 請求汽修人之信息盒子-系統通知數據更新
//
// @authors hjboss <hongjiangproject@yahoo.com> 2015-12-18#
// @version 1.0.0
// @package hhxc
if (!defined('HHXC')) die('Permission denied');

$condition = array(
	'schema' => 'hh_message',
	'fields' => array('*'),
	'filter' => array(
		'zhuangtai' => 1,
	),
	'others' => 'ORDER BY site DESC LIMIT 5',

);

$recordset = StorageFind($condition);
if (is_array($recordset) == FALSE or empty($recordset) == TRUE) {
	$result['msg'] = MESSAGE_EMPTY;
} else {
	$result = array('code' => '101', 'data' => array());

	foreach ($recordset as $index => $row) {
		if ($index == 0) {
			$result['databasever'] = $row['id'];
		}

		$result['data'][] = array(
			'nid'       => $row['id'],
			'title'    => $row['title'],
			'posttime' => $row['createdat'],
			'message'  => $row['content'],
			'url'      => $row['url'] . '&m=mobile',
		);
	}
}

