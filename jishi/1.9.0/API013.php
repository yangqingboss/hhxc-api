<?php
// Copyright 2015 The Haohaoxiuche Team Authors. All right reserved.
// Use of this source that is governed by a Apache-style
// license that can be found in the LICENSE file.
//
// 技術版API編號013 發表汽修人新貼
//
// @authors hjboss <hongjiangproject@yahoo.com> 2015-12-16#
// @version 1.0.0
// @package hhxc
if (!defined('HHXC')) die('Permission denied');

if (CheckOpenID($params['openid'], $params['uid']) == FALSE) {
	$result['msg'] = MESSAGE_WARNING;
} else {
	$data = array(
		'pubuser'   => Assign($params['uid'], 0),
		'type'      => Assign($params['tag'], 1),
		'title'     => Assign($params['title']),
		'content'   => Assign($params['content']),
		'pubtime'   => 'NOW()',
		'anonymous' => Assign($params['anonymous'], 0),
	);

	$num = StorageAdd('hh_techforum', $data);
	if (empty($num) == TRUE) {
		$result['msg'] = '发送失败！';
	} else {
		$result = array('code' => '101', 'data' => array());

		$record = StorageFindID('hh_techforum', $num);
		if (is_array($record) and empty($record) == FALSE) {
			$result['data'][] = array(
				'posttime' => $record['pubtime'],
				'tid'      => $record['id'],
			);
		}
	}
}
