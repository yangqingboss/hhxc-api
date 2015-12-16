<?php
// Copyright 2015 The Haohaoxiuche Team Authors. All right reserved.
// Use of this source that is governed by a Apache-style
// license that can be found in the LICENSE file.
//
// 技術版API編號005 我能解決或者申請難度加一
//
// @authors hjboss <hongjiangproject@yahoo.com> 2015-12-16#
// @version 1.0.0
// @package hhxc
if (!defined('HHXC')) die('Permission denied');

if (CheckOpenID($params['openid'], $params['uid']) == FALSE) {
	$result['msg'] = MESSAGE_WARING;
} else {
	$num = 0;

	switch ($params['type']) {
	case '1':
		$fields = array('lockby' => $params['uid'], 'locktime' => 'NOW()', 'zhuangtai' => 1);
		$filter = array(
			'id'        => Assign($params['qid'], 0),
			'lockby'    => 0,
			'zhuangtai' => 0,
		);

		$num = StorageEdit('hh_questions', $fields, $filter);
		break;

	case '2':
		$data = array(
			'ofq'       => Assign($params['qid'], 0),
			'ofuser'    => Assign($params['uid'], 0),
			'createdat' => 'NOW()',
		);

		$id = StorageAdd('hh_questions_diff', $data);
		if (empty($id) == FALSE) {
			$num = StorageEditByID('hh_questions', array('difficulty' => 'difficulty+1'), $params['qid']);
		}
		break;
	}

	if (empty($num) == FALSE) {
		$result = array('code' => '101', 'msg' => MESSAGE_SUCCESS);
	} else {
		$result['msg'] = '问题已被其他人抢答！';
	}
}

