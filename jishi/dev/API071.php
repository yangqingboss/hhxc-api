<?php
// Copyright 2015 The Haohaoxiuche Team Authors. All right reserved.
// Use of this source that is governed by a Apache-style
// license that can be found in the LICENSE file.
//
// 技術版API編號071 獲取用戶數據
//
// @authors hjboss <hongjiangproject@yahoo.com> 2015-12-18#
// @version 1.0.0
// @package hhxc
if (!defined('HHXC')) die('Permission denied');

if (CheckOpenID($params['openid'], $params['uid']) == FALSE) {
	$result['msg'] = MESSAGE_WARNING;
} else {
	$result = array('code' => '101', 'data' => array());

	$record = StorageFindID('hh_techuser', Assign($params['cuid'], 0));
	if (is_array($record) and empty($record) == FALSE) {
		$h_grade = ($record['grade']>50) ? 'V' . ($record['grade']-50) : 'L' . $record['grade'];

		$result['data'][] = array(
			'image'      => $record['image'],
			'nick'       => $record['nick'],
			'grade'      => $h_grade,
			'city'       => $record['city'],
			'cars'       => $record['cars'],
			'job'        => $record['job'],
			'level'      => $record['level'],
			'experience' => $record['experience'],
		);
	}
}

