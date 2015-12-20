<?php
// Copyright 2015 The Haohaoxiuche Team Authors. All right reserved.
// Use of this source that is governed by a Apache-style
// license that can be found in the LICENSE file.
//
// 技術版API編號106 案例收藏和取消
//
// @authors hjboss <hongjiangproject@yahoo.com> 2015-12-18#
// @version 1.0.0
// @package hhxc
if (!defined('HHXC')) die('Permission denied');

$buf_caseid = empty($params['cid']) ? Assign($params['caseid'], 0) : Assign($params['cid'], 0);
$condition = array(
	'schema' => 'hh_techuser_shoucang',
	'filter' => array(
		'uid' => Assign($params['uid'], 0),
		'tid' => Assign($params['cid'], 0),
		'tag' => '5',
	),
);

$record = StorageFindOne($condition);
if (is_array($record) == FALSE or empty($record) == TRUE) {
	$data = array(
		'uid'      => Assign($params['uid'], 0),
		'tid'      => $buf_caseid,
		'tag'      => 5,
		'type'     => Assign($params['type'], 0),
		'deviceid' => Assign($params['deviceid']),
	);

	$id = StorageAdd('hh_techuser_shoucang', $data);
	if (empty($id) == TRUE) {
		$result['msg'] = '收藏失敗！';
	} else {
		$result = array('code' => '101', 'msg' => $params['type'] == 1 ? '收藏成功！' : '取消收藏！');

		## 兼容舊版數據表
		$data_sub = array(
			'ofuser'    => Assign($params['uid'], 0),
			'createdat' => 'NOW()',
			'ofanli'    => $buf_caseid,
			'search'    => '',
		);
		StorageAdd('hh_techuser_anli', $data_sub);
	}

} else {
	$fields = array(
		'uid'      => Assign($params['uid'], 0),
		'tid'      => $buf_caseid,
		'tag'      => 5,
		'type'     => Assign($params['type'], 0),
		'deviceid' => Assign($params['deviceid']),
	);

	$num = StorageEdit('hh_techuser_shoucang', $fields, $condition['filter']);
	if (empty($num) == TRUE) {
		$result['msg'] = '收藏失敗！';
	} else {
		$result = array('code' => '101', 'msg' => $params['type'] == 1 ? '收藏成功！' : '取消收藏！');
	}

}

if (CheckOpenID($params['openid'], $params['uid']) == TRUE) {
	Techuser_setScore($params['uid'], 3);
}

