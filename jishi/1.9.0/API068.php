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
			'code'     => '101',
			'number1'  => Assign($record['msg1'],  0),
			'number2'  => Assign($record['msg2'],  0),
			'number3'  => Assign($record['msg3'],  0),
			'number4'  => Assign($record['msg4'],  0),
			'number5'  => Assign($record['msg5'],  0),
			'number6'  => Assign($record['msg6'],  0),
			'number7'  => Assign($record['msg7'],  0),
			'number8'  => Assign($record['msg8'],  0),
			'number9'  => Assign($record['msg9'],  0),
			'number10' => Assign($record['msg10'], 0),
			'number11' => Assign($record['msg11'], 0),
			'number12' => Assign($record['msg12'], 0),
			'number13' => Assign($record['msg13'], 0),
			'number14' => Assign($record['msg14'], 0),

			## 附加字段
			'number9_0'  =>  Assign($record['msg9_0'],   0),
			'number9_1'  =>  Assign($record['msg9_1'],   0),
			'number10_0' =>  Assign($record['msg10_0'],  0),
			'number10_1' =>  Assign($record['msg10_1'],  0),
			'number11_0' =>  Assign($record['msg11_0'],  0),
			'number11_1' =>  Assign($record['msg11_1'],  0),
			'number12_0' =>  Assign($record['msg12_0'],  0),
			'number12_1' =>  Assign($record['msg12_1'],  0),

		);
	}
}

