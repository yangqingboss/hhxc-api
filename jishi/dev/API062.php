<?php
// Copyright 2015 The Haohaoxiuche Team Authors. All right reserved.
// Use of this source that is governed by a Apache-style
// license that can be found in the LICENSE file.
//
// 技術版API編號062 匹配正時之獲取車型詳細信息 ##代替048
//
// @authors hjboss <hongjiangproject@yahoo.com> 2015-12-18#
// @version 1.0.0
// @package hhxc
if (!defined('HHXC')) die('Permission denied');

$condition = array(
	'schema' => 'car_type_zhengshi',
	'fields' => array('id', 'title'),
	'flter' => array(
		'oftype' => Assign($params['bid'], 0),
		'type'   => Assign($params['tid'], 0),
	),
);

$recordset = StorageFind($condition);
if (is_array($recordset) == FALSE or empty($recordset) == TRUE) {
	$result['msg'] = '没有新数据，或者出现错误！';
} else {
	$result = array('code' => '101', 'data' => array());

	foreach ($recordset as $index => $row) {
		$result['data'][] = array(
			'id'   => $row['id'],
			'name' => $row['title'],
		);
	}
}

