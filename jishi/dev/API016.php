<?php
// Copyright 2015 The Haohaoxiuche Team Authors. All right reserved.
// Use of this source that is governed by a Apache-style
// license that can be found in the LICENSE file.
//
// 技術版API編號016 技師用戶登陸請求 ##已取消 新接口064
//
// @authors hjboss <hongjiangproject@yahoo.com> 2015-12-17#
// @version 1.0.0
// @package hhxc
if (!defined('HHXC')) die('Permission denied');

$condition = array(
	'schema' => 'hh_techuser',
	'filter' => array(
		'username_d' => Assign($params[KEY_PHONE]),
	),
);

$record = StorageFindOne($condition);
if (is_array($record) == FALSE or empty($record) == TRUE) {
	$result['msg'] = '用户不存在！';
} else {
	$condition['filter']['password'] = Assign($params['pwd']);

	$record = StorageFindOne($condition);
	if (is_array($record) == FALSE or empty($record) == TRUE) {
		$result['msg'] = '密码不正确！';
	} else {
		$result = array('code' => '101', 'msg' => Assign($params[KEY_PHONE]), 'data' => array());
		$result['data'][] = array(
			'uid'        => $record['id'],
			'openid'     => $record['loginid'],
			'image'      => $record['headerimg'],
			'nick'       => $record['nick'],
			'grade'      => $record['grade'],
			'score'      => $record['score'],
			'city'       => $record['city'],
			'cars'       => $record['cars'],
			'job'        => $record['job'],
			'level'      => $record['level'],
			'experience' => $record['experience'],
		);

		Techuser_setScore($record['id'], 1);
	}
}

