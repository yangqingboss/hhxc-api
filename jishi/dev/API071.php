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

$rankings = array('10', '100', '1k', '5k', '1w', '2w', '3w', '4w', '5w', '6w', '10w');

if (CheckOpenID($params['openid'], $params['uid']) == FALSE) {
	$result['msg'] = MESSAGE_WARNING;
} else {
	$record = StorageFindID('hh_techuser', Assign($params['cuid'], 0));
	if (is_array($record) == FALSE or empty($record) == TRUE) {
		$result['msg'] = MESSAGE_EMPTY;
	} else {
		$result = array('code' => '101', 'data' => array());
		$h_grade = ($record['grade']>50) ? 'V' . ($record['grade']-50) : 'L' . $record['grade'];
		
		## 獲取經驗名稱
		$condition_main = array(
			'schema' => array('hh_rank'),
			'fields' => array(
				'*',
				'(SELECT score FROM hh_rank WHERE id=t0.id+1) AS h_score',
			),
			'filter' => array(
				'dengji' => $record['rankname'],
			),
		);
		$h_rankname = StorageFindOne($condition_main);
		$result['data'][] = array(
			'image'      => $record['image'],
			'nick'       => $record['nick'],
			'grade'      => $h_grade,
			'city'       => $record['city'],
			'cars'       => $record['cars'],
			'job'        => $record['job'],
			'level'      => $record['level'],
			'experience' => $record['experience'],

			## 兼容字段
			'official'   => Assign($record['type'], 0),
			'identified' => Assign($record['identified'], 0),
			'rank'       => Assign($record['rank'], 0),
			'rankname'   => Assign($h_rankname['title']),
			'rankvalue'  => Assign($h_rankname['h_score']),
			'ranking'    => $rankings[count($rankings) - 1],
		);
	}
}

