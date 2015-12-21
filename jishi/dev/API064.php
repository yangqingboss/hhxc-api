<?php
// Copyright 2015 The Haohaoxiuche Team Authors. All right reserved.
// Use of this source that is governed by a Apache-style
// license that can be found in the LICENSE file.
//
// 技術版API編號064 用戶登陸請求 ##代替016
//
// @authors hjboss <hongjiangproject@yahoo.com> 2015-12-18#
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
	$result['msg'] = "用户{$params[KEY_PHONE]}不存在！";

} else {
	$condition['filter']['password'] = Assign($params['pwd']);

	$record = StorageFindOne($condition);
	if (is_array($record) == FALSE or empty($record) == TRUE) {
		$result['msg'] = '密码不正确！';
	} else {
		$result = array('code' => '101', 'msg' => $params[KEY_PHONE], 'data' => array());

		## 更新OPENID
		$fields = array(
			'loginid'  => md5($record['uid'] . $params['password'] . time()),
			'deviceid' => Assign($params['deviceid']),
		);
		StorageEditByID('hh_techuser', $fields, $record['id']);

		## 獲取當前用戶信息
		$condition_sub = array(
			'schema' => 'hh_techuser',
			'fields' => array(
				'*',
				'(SELECT title FROM hh_score WHERE dengji=grade) AS h_grade',
			),
			'filter' => array(
				'id' => $record['id'],
			),
		);
		$buf = StorageFindOne($condition_sub);
		if (is_array($buf) and empty($buf) == FALSE) {
			$result['data'][] = array(
				'uid'        => $buf['id'],
				'openid'     => $buf['loginid'],
				'image'      => $buf['headerimg'],
				'nick'       => $buf['nick'],
				'grade'      => $buf['h_grade'],
				'score'      => $buf['score'],
				'city'       => $buf['city'],
				'cars'       => $buf['cars'],
				'job'        => $buf['job'],
				'level'      => $buf['level'],
				'experience' => $buf['experience'],
				'percent'    => $buf['percent'],
				'needscore'  => $buf['needscore'],
			);

			Techuser_setScore($record['id'], 1);
		}

		## 更新登陸積分

	}
}

