<?php
// Copyright 2015 The Haohaoxiuche Team Authors. All right reserved.
// Use of this source that is governed by a Apache-style
// license that can be found in the LICENSE file.
//
// 技術版API編號090 汽修人之招聘新貼
//
// @authors hjboss <hongjiangproject@yahoo.com> 2015-12-18#
// @version 1.0.0
// @package hhxc
if (!defined('HHXC')) die('Permission denied');

if (CheckOpenID($params['openid'], $params['uid']) == FALSE and FALSE) {
	$result['msg'] = MESSAGE_WARNING;
} else {
	$data = array(
		'ofuser'      => Assign($params['uid'], 0),
		'job'         => Assign($params['job']),
		'salary'      => Assign($params['salary']),
		'headcount'   => Assign($params['headcount']),
		'city'        => Assign($params['city']),
		'contactinfo' => Assign($params['contactinfo']),
		'boon'        => Assign($params['boon']),
		'business'    => Assign($params['business']),
		'scale'       => Assign($params['scale']),
		'name'        => Assign($params['name']),
		'location'    => Assign($params['location']),
		'etc'         => Assign($params['etc']),
		'createdat'   => 'NOW()',
	);
	$id = StorageAdd('hh_zhaopin', $data);
	if (empty($id)) {
		$result['msg'] = '发送失败！';
	} else {
		$result = array('code' => '101', 'data' => array());

		$record = StorageFindID('hh_zhaopin', $id);
		if (empty($record) == FALSE) {
			$result['data'][] = array(
				'posttime' => $record['createdat'],
				'tid'      => $record['id'],
			);
		}
	}
}

