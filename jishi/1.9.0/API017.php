<?php
// Copyright 2015 The Haohaoxiuche Team Authors. All right reserved.
// Use of this source that is governed by a Apache-style
// license that can be found in the LICENSE file.
//
// 技術版API編號017 更改用戶個人信息
//
// @authors hjboss <hongjiangproject@yahoo.com> 2015-12-17#
// @version 1.0.0
// @package hhxc
if (!defined('HHXC')) die('Permission denied');

if (CheckOpenID($params['openid'], $params['uid']) == FALSE) {
	$result['msg'] = MESSAGE_WARNING;
} else {
	$fields = array(); $image_name = '';

	## 上傳用戶頭像
	if (empty($_FILES['image']['error']) == TRUE) {
		$image_name = Assign($params['uid'], 0) . '_' . time() . '.png';
		move_uploaded_file($_FILES['image']['tmp_name'], IMAGE_ROOT . DIRECTORY_SEPARATOR . $image_name);
	}

	## 處理提交數據
	$keys = array('nick', 'city', 'cars', 'job', 'level', 'experience');
	foreach ($keys as $index => $key) {
		if (empty($params[$key]) == FALSE) {
			$fields[$key] = $params[$key];
		}
	}
	if (empty($image_name) == FALSE) $fields['headerimg'] = $image_name;
	if (empty($fields) == FALSE) {
		$num = StorageEditByID('hh_techuser', $fields, $params['uid']);
		$result = array('code' => '101', 'image' => $image_name);
	}
}

