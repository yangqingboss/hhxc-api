<?php
// Copyright 2015 The Haohaoxiuche Team Authors. All right reserved.
// Use of this source that is governed by a Apache-style
// license that can be found in the LICENSE file.
//
// 技術版API編號098 綁定手機號 ##參考015和096
//
// @authors hjboss <hongjiangproject@yahoo.com> 2015-12-18#
// @version 1.0.0
// @package hhxc
if (!defined('HHXC')) die('Permission denied');

if (CheckOpenID($params['openid'], $params['uid']) == FALSE) {
	$result['msg'] = '电话号码已注册！';
} else {
	$fields = array(
		'username_d' => Assign($params[KEY_PHONE]),
		'password'   => Assign($params['pwd']),
	);

	$num = StorageEditByID('hh_techuser', $fields, $params['uid']);
	if ($num <= 0) {
		$result['msg'] = '绑定失败！';
	} else {
		$result = array('code' => '101', 'msg' => '绑定成功！');
	}
}

