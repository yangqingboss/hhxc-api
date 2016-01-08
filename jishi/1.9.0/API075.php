<?php
// Copyright 2015 The Haohaoxiuche Team Authors. All right reserved.
// Use of this source that is governed by a Apache-style
// license that can be found in the LICENSE file.
//
// 技術版API編號075 提交案例評分
//
// @authors hjboss <hongjiangproject@yahoo.com> 2015-12-18#
// @version 1.0.0
// @package hhxc
if (!defined('HHXC')) die('Permission denied');

if (CheckOpenID($params['openid'], $params['uid']) == FALSE) {
	$result['msg'] = MESSAGE_WARNING;
} else {
	$data = array(
		'ofuser'    => Assign($params['uid'], 0),
		'createdat' => 'NOW()',
		'ofanli'    => Assign($params['cid'], 0),
		'score'     => Assign($params['score'], 0),
	);

	$id = StorageAdd('car_anli_score', $data);
	if (empty($id) == TRUE) {
		$result['msg'] = '重复保存！';

	} else {
		$avg = "(SELECT SUM(score)/COUNT(id) FROM car_anli_score WHERE ofanli='%d')";
		$fields = array(
			'score_avg' => sprintf($avg, $params['cid']),
		);
		StorageEditByID('search_result', $fields, $params['cid']);

		## 更新用戶積分
		Techuser_setScore(Assign($params['uid'], 0), 5);

		$result = array('code' => '101', 'msg' => MESSAGE_SUCCESS);
	}
}
