<?php
// Copyright 2015 The Haohaoxiuche Team Authors. All right reserved.
// Use of this source that is governed by a Apache-style
// license that can be found in the LICENSE file.
//
// 技術版API編號014 發表汽修人跟帖
//
// @authors hjboss <hongjiangproject@yahoo.com> 2015-12-16#
// @version 1.0.0
// @package hhxc
if (!defined('HHXC')) die('Permission denied');

if (CheckOpenID($params['openid'], $params['uid']) == FALSE) {
	$result['msg'] = MESSAGE_WARING;
} else {
	$schema = 'hh_techforum_list'; $schema_s = 'hh_techforum';

	if ($params['tag'] == '3') {
		$schema   = 'hh_techqzhi_list';
		$schema_s = 'hh_techqzhi';
	}

	$data   = array(
		'pubuser'   => Assign($params['uid'], 0),
		'type'      => Assign($params['tag'], 0),
		'tid'       => Assign($params['tid'], 0),
		'content'   => Assign($params['content']),
		'pubtime'   => 'NOW()',
		'anonymous' => Assign($params['anonymous'], 0),
		'no'        => "(SELECT maxno FROM {$schema_s} WHERE id='{$params['tid']}')",
	);

	$id = StorageAdd($schema, $data);
	if (empty($id) == TRUE) {
		$result['msg'] = '发送失败！';
	} else {
		$result = array('code' => '101', 'data' => array());

		$fields = array(
			'maxno'      => 'maxno+1',
			'replycount' => "(SELECT COUNT(*) FROM {$schema} WHERE tid='{$params['tid']}')",
		);
		StorageEditByID($schema_s, $fields, Assign($params['tid'], 0));

		$record = StorageFindID($schema, $id, '*');
		if (is_array($record) and empty($record) == FALSE) {
			$result['data'][] = array(
				'posttime' => $record['pubtime'],
				'index'    => $record['no'],
			);
		}
	}
}
