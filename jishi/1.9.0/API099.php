<?php
// Copyright 2015 The Haohaoxiuche Team Authors. All right reserved.
// Use of this source that is governed by a Apache-style
// license that can be found in the LICENSE file.
//
// 技術版API編號099 汽修人收藏
//
// @authors hjboss <hongjiangproject@yahoo.com> 2015-12-18#
// @version 1.0.0
// @package hhxc
if (!defined('HHXC')) die('Permission denied');

if (CheckOpenID($params['openid'], $params['uid']) == FALSE) {
	$result['msg'] = MESSAGE_WARNING;
} else {
	$result['msg'] = '收藏失败！';

	$condition = array(
		'schema' => 'hh_techuser_shoucang',
		'filter' => array(
			'uid' => Assign($params['uid'], 0),
			'tid' => Assign($params['tid'], 0),
			'tag' => Assign($params['tag'], 0),
		),
	);

	$record = StorageFindOne($condition);
	if (is_array($record) and empty($record) == FALSE) {
		$fields = array(
			'type' => Assign($params['type'], 0),
		);

		$num = StorageEdit('hh_techuser_shoucang', $fields, $condition['filter']);
		$message = empty($params['type']) ? '取消成功！' : '收藏成功！';
		$result  = array('code' => '101', 'msg' => $message);

	} else {
		$data = array(
			'uid'      => Assign($params['uid'], 0),
			'tid'      => Assign($params['tid'], 0),
			'tag'      => Assign($params['tag'], 0),
			'type'     => Assign($params['type'], 0),
			'deviceid' => Assign($params['deviceid']),
		);

		$id = StorageAdd('hh_techuser_shoucang', $data);
		if (empty($id) == FALSE) {
			$message = empty($params['type']) ? '取消成功！' : '收藏成功！';
			$result = array('code' => '101', 'msg' => $message);
		}
	}
}
