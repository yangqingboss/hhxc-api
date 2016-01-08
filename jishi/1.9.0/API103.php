<?php
// Copyright 2015 The Haohaoxiuche Team Authors. All right reserved.
// Use of this source that is governed by a Apache-style
// license that can be found in the LICENSE file.
//
// 技術版API編號103 查案例-獲取案例詳細信息 ##代替058
//
// @authors hjboss <hongjiangproject@yahoo.com> 2015-12-18#
// @version 1.0.0
// @package hhxc
if (!defined('HHXC')) die('Permission denied');

if (CheckOpenID($params['openid'], $params['uid']) == FALSE) {
	$result['msg'] = MESSAGE_WARNING;
} else {
	$the_times = CheckTimes($params['openid'], $params['uid']);
	$url_anli = sprintf(PAGE_ANLI, $params['uid'], $params['openid'], DEBUG);

	if (empty($the_times) == FALSE) {
		$result = array('code' => '101');

		$record = StorageFindID('search_result', Assign($params['cid'], 0));
		if (is_array($record) and empty($record) == FALSE) {
			$result['caseurl'] = $url_anli . $record['id'];
		}
	} else {
		$condition_sub = array(
			'schema' => 'hh_techuser',
			'fields' => array('grade'),
			'filter' => array(
				'id' => Assign($params['uid'], 0),
			),
		);
		$condition = array(
			'schema' => 'hh_score',
			'fields' => array('chakan'),
			'filter' => '(' . SQLSub($condition_sub) . ')',
		);

		$record = StorageFindOne($condition);
		if (is_array($record) and empty($record) == FALSE) {
			$result = array('code' => '103', 'msg' => $recod['chakan']);
		}
	}
}

