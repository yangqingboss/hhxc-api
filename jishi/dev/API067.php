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
	'schema' => 'hh_sys_msg',
	'filter' => array(
		'zhuangtai' => 1,
		'id'        => array('GT', Assign($params['databasever'], 0)),
	),
	'others' => 'ORDER BY id DESC LIMIT 5',
);

$recordset = StorageFind($condition);
if (is_array($recordset) and empty($recordset) == FALSE) {
	$result['msg'] = MESSAGE_EMPTY;
} else {
	$result = array('code' => '101', 'data' => array());

	foreach ($recordset as $index => $row) {
		if ($index == 0) {
			$result['databasever'] = $row['id'];
		}

		$result['data'][] = array(
			'id'       => $row['id'],
			'title'    => $row['title'],
			'posttime' => $row['createdat'],
			'message'  => $row['content'],
		);
	}
}

