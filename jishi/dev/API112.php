<?php
// Copyright 2015 The Haohaoxiuche Team Authors. All right reserved.
// Use of this source that is governed by a Apache-style
// license that can be found in the LICENSE file.
//
// 技術版API編號112 懸賞值列表
//
// @authors hjboss <hongjiangproject@yahoo.com> 2015-12-18#
// @version 1.0.0
// @package hhxc
if (!defined('HHXC')) die('Permission denied');

if (CheckOpenID($params['openid'], $params['uid']) == FALSE) {
	$result['msg'] = MESSAGE_WARNING;
} else {
	$result = array('code' => '101', 'data' => array());

	$record = StorageFindID('hh_techuser', $params['uid']); $buffer = array();
	$scores = array(50, 100, 150, 200, 300, 500);
	foreach ($scores as $score) {
		if ($score < Techuser_viewRankScore($record['rankscore'])) {
			$buffer['datavalue'][] = $score;
		}
	}

	$buffer['allreward'] = $record['rankscore'];
	$buffer['changevalue'] = RANK_RS2R;
	$result['data'][] = $buffer;
	//$result['msg'] = sprintf(ASK_MESSAGE, $record['rankscore'], RANK_RS2R);
}
