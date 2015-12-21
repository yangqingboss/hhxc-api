<?php
// Copyright 2015 The Haohaoxiuche Team Authors. All right reserved.
// Use of this source that is governed by a Apache-style
// license that can be found in the LICENSE file.
//
// 技術版API編號065 更新用戶積分和等級信息 ##代替051
//
// @authors hjboss <hongjiangproject@yahoo.com> 2015-12-18#
// @version 1.0.0
// @package hhxc
if (!defined('HHXC')) die('Permission denied');

if (CheckOpenID($params['openid'], $params['uid']) == FALSE) {
	$result['msg'] = MESSAGE_WARNING;
} else {
	$condition = array(
		'schema' => 'hh_techuser',
		'fields' => array(
			'*',
			'(SELECT title FROM hh_score WHERE dengji=grade) AS h_grade',
		),
		'filter' => array(
			'id' => Assign($params['uid'], 0),
		),
	);

	$record = StorageFindOne($condition);
	if (is_array($record) and empty($record) == FALSE) {
		$result = array(
			'code'      => '101',
			'grade'     => $record['h_grade'],
			'score'     => $record['score'],
			'percent'   => $record['percent'],
			'needscore' => $record['needscore'],
		);
	}
}

