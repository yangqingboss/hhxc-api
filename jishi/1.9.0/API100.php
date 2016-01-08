<?php
// Copyright 2015 The Haohaoxiuche Team Authors. All right reserved.
// Use of this source that is governed by a Apache-style
// license that can be found in the LICENSE file.
//
// 技術版API編號100 汽修人點贊
//
// @authors hjboss <hongjiangproject@yahoo.com> 2015-12-18#
// @version 1.0.0
// @package hhxc
if (!defined('HHXC')) die('Permission denied');

if (CheckOpenID($params['openid'], $params['uid']) == FALSE) {
	$result['msg'] = MESSAGE_WARNING;
} else {
	$result['msg'] = '点赞失败！';

	$condition = array(
		'schema' => 'hh_techuser_dianzan',
		'filter' => array(
			'uid'   => Assign($params['uid'], 0),
			'tid'   => Assign($params['tid'], 0),
			'tag'   => Assign($params['tag'], 0),
			'touid' => 0,
		),
	);

	$record = StorageFindOne($condition);
	if (is_array($record) and empty($record) == FALSE) {
		$fields = array(
			'type' => Assign($params['type'], 0),
		);

		$num = StorageEdit('hh_techuser_dianzan', $fields, $condition['filter']);
		$message = empty($params['type']) ? '取消成功！' : '点赞成功！';
		$result  = array('code' => '101', 'msg' => $message);

	} else {
		$data = array(
			'uid'      => Assign($params['uid'], 0),
			'tid'      => Assign($params['tid'], 0),
			'tag'      => Assign($params['tag'], 0),
			'type'     => Assign($params['type'], 0),
			'deviceid' => Assign($params['deviceid']),
			'touid'    => 0,
		);

		$id = StorageAdd('hh_techuser_dianzan', $data);
		if (empty($id) == FALSE) {
			$message = empty($params['type']) ? '取消成功！' : '点赞成功！';
			$result = array('code' => '101', 'msg' => $message);
		}
	}

	## 更新點贊通知
	$schemas = array(
		'1' => 'hh_techforum', 
		'2' => 'hh_techforum', 
		'3' => 'hh_techqzhi',
		'4' => 'hh_zhaopin',
	);
	$info = StorageFindID($schemas[$params['tag']], Assign($params['tid'], 0));
	$pubuser = $params['tag'] == '4' ? 'ofuser' : 'pubuser';
	if (empty($schemas[$params['tag']]) == FALSE and $info[$pubuser] != $params['uid']) {
		$isnewdz = array('isnewdz' => $params['type'] === '1' ? 1 : 0);
		StorageEditByID($schemas[$params['tag']], $isnewdz, Assign($params['tid'], 0));

		## 被點贊的樓主更新通知
		RefreshMsg(Assign($info[$pubuser], 0));
	}

	## 推送消息
	if ($params['type'] == '1') {
		$schema = 'hh_techforum';
		switch ($params['tag']) {
		case '3':
			$schema = 'hh_techqzhi';
			break;
	
		case '4':
			$schema = 'hh_zhaopin';
			break;
		}
		$params['touid'] = 0;
		JPushMessage("1030{$params['tag']}", $params, $schema);
	}
}
