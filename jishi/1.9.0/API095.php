<?php
// Copyright 2015 The Haohaoxiuche Team Authors. All right reserved.
// Use of this source that is governed by a Apache-style
// license that can be found in the LICENSE file.
//
// 技術版API編號095 邀請好友
//
// @authors hjboss <hongjiangproject@yahoo.com> 2015-12-18#
// @version 1.0.0
// @package hhxc
if (!defined('HHXC')) die('Permission denied');

if (CheckOpenID($params['openid'], $params['uid']) == FALSE) {
	$result['msg'] = MESSAGE_WARNING;
} else {
	$result = array('code' => '101', 'msg' => 'exclusive');

	$record = StorageFindID('hh_techuser', Assign($params['uid'], 0));
	$result['withcode'] = (is_array($record) and empty($record) == FALSE) ? $record['withcode'] : '';

	$condition = array(
		'schema' => 'hh_dbver',
		'filter' => array(
			'vername' => 'yaoqinwenan',
		),
	);
	$buf = StorageFindOne($condition);
	if (is_array($buf) and empty($buf) == FALSE) {
		$result['information'] = str_replace('_code_', $result['withcode'], $result['remark']);
	}
}

