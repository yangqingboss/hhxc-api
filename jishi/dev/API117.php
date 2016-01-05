<?php
// Copyright 2015 The Haohaoxiuche Team Authors. All right reserved.
// Use of this source that is governed by a Apache-style
// license that can be found in the LICENSE file.
//
// 技術版API編號117 获取关键词列表
//
// @authors hjboss <hongjiangproject@yahoo.com> 2015-12-18#
// @version 1.0.0
// @package hhxc
if (!defined('HHXC')) die('Permission denied');

$condition = array(
	'schema' => 'car_indexword',
	'fields' => array('*'),
	'filter' => array(
		'cateid' => Assign($params['faultType'], 0),
		'grade'  => array('GT', 0),
	),
);
$recordset = StorageFind($condition);
if (is_array($recordset) == FALSE or empty($recordset) == TRUE) {
	$result['msg'] = MESSAGE_EMPTY;
} else {
	$result = array('code' => '101', 'data' => array());

	foreach ($recordset as $number => $row) {
		$result['data'][] = array(
			'keywordType' => Assign($row['keyid'], 0),
			'keywordName' => Assign($row['curword']),
			'data'        => array(),
		);
	}
}

