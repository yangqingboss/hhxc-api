<?php
// Copyright 2015 The Haohaoxiuche Team Authors. All right reserved.
// Use of this source that is governed by a Apache-style
// license that can be found in the LICENSE file.
//
// 技術版API編號020 汽修人之發布求職新貼
//
// @authors hjboss <hongjiangproject@yahoo.com> 2015-12-17#
// @version 1.0.0
// @package hhxc
if (!defined('HHXC')) die('Permission denied');

if (CheckOpenID($params['openid'], $params['uid']) == FALSE) {
	$result['msg'] = MESSAGE_WARING;
} else {
	$data = array(
		'pubuser'    => Assign($params['uid'], 0),
		'pubtime'    => 'NOW()',
		'level'      => Assign($params['level']),
		'experience' => Assign($params['experience']),
		'city'       => Assign($params['city']),
		'cars'       => Assign($params['cars']),
		'job'        => Assign($params['job']),
		'introduce'  => Assign($params['introduce']),
	);

	$id = StorageAdd('hh_techqzhi', $data);
	if (empty($id) == TRUE) {
		$result['msg'] = '发送失败！';
	} else {
		$result = array('code' => '101', 'data' => array());

		$record = StorageFindID('hh_techqzhi', $id);
		if (is_array($record) and empty($record) == FALSE) {
			$result['data'][] = array(
				'posttime' => $record['pubtime'],
				'tid'      => $record['id'],
			);
		}
	}
}

