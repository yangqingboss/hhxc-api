<?php
// Copyright 2015 The Haohaoxiuche Team Authors. All right reserved.
// Use of this source that is governed by a Apache-style
// license that can be found in the LICENSE file.
//
// 技術版API編號007 申請訪問該問題
//
// @authors hjboss <hongjiangproject@yahoo.com> 2015-12-16#
// @version 1.0.0
// @package hhxc
if (!defined('HHXC')) die('Permission denied');

if (CheckOpenID($params['openid'], $params['uid']) == FALSE) {
	$result['msg'] = MESSAGE_WARNING;
} else {
	$fields = array('lockby' => 0, 'locktime' => 'NOW()', 'zhuangtai' => 0);
	$filter = array(
		'id'        => Assign($params['qid'], 0),
		'lockby'    => Assign($params['uid'], 0),
		'zhuangtai' => 1,
	);

	$num = StorageEdit('hh_questions', $fields, $filter);
	if (empty($num) == TRUE) {
		$result['msg'] = '放弃失败！';
	} else {
		StorageEdit('hh_questions_list', array('zhuangtai' => 1), array('qid' => Assign($params['qid'], 0)));
		$result = array('code' => '101', 'msg' => MESSAGE_SUCCESS);
	}
}
