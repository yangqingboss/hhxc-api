<?php
// Copyright 2015 The Haohaoxiuche Team Authors. All right reserved.
// Use of this source that is governed by a Apache-style
// license that can be found in the LICENSE file.
//
// 技術版API編號018 汽修人之獲取求職數據 ##已取消
//
// @authors hjboss <hongjiangproject@yahoo.com> 2015-12-17#
// @version 1.0.0
// @package hhxc
if (!defined('HHXC')) die('Permission denied');

$condition = array(
	'schema' => array('hh_techqzhi'),
	'fields' => array(
		'*',
		'(SELECT nick FROM hh_techuser WHERE id=t0.pubuser) AS h_nick',
		'(SELECT headerimg FROM hh_techuser WHERE id=t0.pubuser) AS h_headerimg',
	),
	'filter' => array('id' => array('LT', Assign($params['tid'], 0))),
	'others' => 'ORDER BY id DESC LIMIT 10',
);
if (empty($params['tid']) == TRUE) {
	$condition['filter'] = array('id' => array('GT', Assign($params['databasever'], 0)));
}

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
			'uid'        => $row['pubuser'],
			'userpic'    => $row['h_headerimg'],
			'usernick'   => $row['h_nick'],
			'posttime'   => $row['pubtime'],
			'level'      => $row['level'],
			'experience' => $row['experience'],
			'city'       => $row['city'],
			'tid'        => $row['id'],
			'messages'   => $row['replycount'],
		);
	}
}
