<?php
// Copyright 2015 The Haohaoxiuche Team Authors. All right reserved.
// Use of this source that is governed by a Apache-style
// license that can be found in the LICENSE file.
//
// 技術版API編號015 發送用戶註冊請求
//
// @authors hjboss <hongjiangproject@yahoo.com> 2015-12-17#
// @version 1.0.0
// @package hhxc
if (!defined('HHXC')) die('Permission denied');

$data = array(
	'username'   => Assign($params['phone']),
	'password'   => Assign($params['pwd']),
	'createdat'  => 'NOW()',
	'deviceid'   => Assign($params['deviceid']),
	'username_d' => Assign($params[KEY_PHONE]),
);
$id = StorageAdd('hh_techuser', $data);
if (empty($id) == TRUE) {
	$result['msg'] = '电话号码已注册！';
} else {
	$result = array('code' => '101', 'msg' => MESSAGE_SUCCESS);
	
	$num = StorageEditByID('hh_techuser', array('withcode' => WithCode($username, $id)), $id);
	if (empty($num) == TRUE) {
		StorageEditByID('hh_techuser', array('withcode' => WithCode($username, $id, 9999999)), $id);
	}

	Techuser_setScore($id, 7);
}
