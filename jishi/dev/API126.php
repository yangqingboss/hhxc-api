<?php
// Copyright 2015 The Haohaoxiuche Team Authors. All right reserved.
// Use of this source that is governed by a Apache-style
// license that can be found in the LICENSE file.
//
// 技術版API編號126 
//
// @authors hjboss <hongjiangproject@yahoo.com> 2015-12-18#
// @version 1.0.0
// @package hhxc
if (!defined('HHXC')) die('Permission denied');

switch ($params['type']) {
## 用户身份认证
case '1':
	$record_identification = StorageFindID('hh_identification', Assign($params['tid'], 0));
	if (is_array($record_identification) and empty($record_identification) == FALSE) {
		## 更新認證狀態
		$fields = array(
			'status' => Assign($params['status'], 0),
		);
		StorageEditByID('hh_identification', $fields, Assign($params['tid'], 0));

		## 若認證通過
		if ($params['status'] == '3') {
			## 更新用戶信息之認真狀態
			$fields = array(
				'identified' => 1,
			);
			StorageEditByID('hh_techuser', $fields, Assign($record_identification['uid'], 0));

			## 添加用戶經驗值
			Techuser_setRank($record_identification['uid'], 3);
		}

		$result = array('code' => '101', 'msg' => MESSAGE_SUCCESS);
	}
	break;

## 系統通知推送
case '2':
	$message = StorageFindID('hh_message', Assign($params['tid'], 0));
	if (is_array($message) and empty($message) == FALSE) {
		if ($message['zhuangtai'] == '1') {
			$mid = JPushMessageByAll($message['title']);

			$result = array('code' => '101', 'message' => $mid);
		}
	}
}
