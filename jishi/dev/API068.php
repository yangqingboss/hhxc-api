<?php
// Copyright 2015 The Haohaoxiuche Team Authors. All right reserved.
// Use of this source that is governed by a Apache-style
// license that can be found in the LICENSE file.
//
// 技術版API編號068 汽修人之信息盒子-系統通知數據更新
//
// @authors hjboss <hongjiangproject@yahoo.com> 2015-12-18#
// @version 1.0.0
// @package hhxc
if (!defined('HHXC')) die('Permission denied');

if (CheckOpenID($params['openid'], $params['uid']) == FALSE and empty($params['uid']) == FALSE) {
	$result['msg'] = MESSAGE_WARNING;
} else {
	$record = StorageFindID('hh_techuser', $params['uid']);

	if (is_array($record) and empty($record) == FALSE) {
		$result = array(
			'code'    => '101',
			'number1' => $record['msg1'],
			'number2' => $record['msg2'],
			'number3' => $record['msg3'],
			'number4' => $record['msg4'],
			'number5' => $record['msg5'],
			'number6' => $record['msg6'],
			'number7' => $record['msg7'],
			'number8' => $record['msg8'],
		);
	}
}

