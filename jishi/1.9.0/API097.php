<?php
// Copyright 2015 The Haohaoxiuche Team Authors. All right reserved.
// Use of this source that is governed by a Apache-style
// license that can be found in the LICENSE file.
//
// 技術版API編號097 第三方登陸請求 ##參考064
//
// @authors hjboss <hongjiangproject@yahoo.com> 2015-12-18#
// @version 1.0.0
// @package hhxc
if (!defined('HHXC')) die('Permission denied');

$condition = array(
	'schema' => 'hh_techuser',
	'fields' => '*',
	'filter' => array(
		'thirduid' => Assign($params['uid'], 0),
	),
);
$record = StorageFindOne($condition);

$uid = 0; $old_img = ''; $img_path = '';
if (is_array($record) and empty($record) == FALSE) {
	$uid     = $record['id'];
	$old_img = $record['headerimg'];
} else {
	$img_path = get_threeimg($params['uid'], $params['iconurl']);

	$data = array(
		'thirduid'  => Assign($params['uid'], 0),
		'nick'      => Assign($params['nick']),
		'city'      => Assign($params['city']),
		'tag'       => Assign($params['tag'], 0),
		'headerimg' => $img_path,
	);
	$uid = StorageAdd('hh_techuser', $data);
} 

if (empty($uid) == TRUE) {
	$result['msg'] = '登陆不存在！';
} else {
	$record = StorageFindOne($condition);

	if (is_array($record) == FALSE or empty($record) == TRUE) {
		$result['msg'] = '登陆失败！';
	} else {
		$fields = array(
			'loginid'  => md5($params['uid'] . $params['pwd'] . time()),
			'deviceid' => Assign($params['deviceid']),
		);
		StorageEditByID('hh_techuser', $fields, $uid);

		if (empty($old_img) == TRUE) {
			$img_path = get_threeimg($params['uid'], $params['iconurl']);
			StorageEditByID('hh_techuser', array('headerimg' => $img_path), $uid);
		}

		$result = array('code' => '101', 'data' => array());
		$condition_user = array(
			'schema' => 'hh_techuser',
			'fields' => array(
				'*',
				'(SELECT title FROM hh_score WHERE dengji=grade)    AS h_grade',
			),
			'filter' => array(
				'id' => $uid,
			),
		);
		$record_user = StorageFindOne($condition_user);
		
		$icon_path = $record_user['headerimg'];
		if (empty($icon_path) or strpos($icon_path, 'http') > -1) {
			$icon_path = $img_path;
		}

		$rankinfo = StorageFindID('hh_rank', $record_user['rankname']+1);
		$result['data'][] = array(
			'uid'        => Assign($record_user['id'], 0),
			'phone'      => Assign($record_user['username_d']),
			'openid'     => Assign($record_user['loginid']),
			'image'      => $icon_path,
			'nick'       => Assign($record_user['nick']),
			'grade'      => Assign($record_user['h_grade']),
			'score'      => Assign($record_user['score']),
			'city'       => Assign($record_user['city']),
			'cars'       => Assign($record_user['cars']),
			'job'        => Assign($record_user['job']),
			'level'      => Assign($record_user['level']),
			'experience' => Assign($record_user['experience']),
			'percent'    => Assign($record_user['percent']),
			'needscore'  => Assign($record_user['needscore']),
			
			'official'   => Assign($record_user['type'], 0),
			'identified' => Assign($record_user['identified'], 0),
			'rank'       => Assign($record_user['rank'], 0),
			'rankname'   => $rankinfo['title'],

		);

		
		

		Techuser_setScore($uid, 1);
	}
}

