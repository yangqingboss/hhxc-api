<?php
// Copyright 2015 The Haohaoxiuche Team Authors. All right reserved.
// Use of this source that is governed by a Apache-style
// license that can be found in the LICENSE file.
//
// 技術版API編號101 請求發送短信驗證碼
//
// @authors hjboss <hongjiangproject@yahoo.com> 2015-12-18#
// @version 1.0.0
// @package hhxc
if (!defined('HHXC')) die('Permission denied');

$chars = RandomChars();
switch ($params['opt']) {
	case '0':
		$condition = array(
			'schema' => 'hh_techuser',
			'filter' => array(
				'username_d' => Assign($params['phone']),
			),
		);
		if (empty($params['phoneaes']) == FALSE) {
			$condition['filter']['username'] = Assign($params['phoneaes']);
		}

		$record = StorageFindOne($condition);
		if (is_array($record) and empty($record) == FALSE) {
			$result['msg'] = '该号码已经被注册！';
			die(JsonEncode($result));
		}

		$params['message'] = SMS_ACTION_REGISTER . $chars;
		break;

	case '1':
		$condition = array(
			'schema' => 'hh_techuser',
			'filter' => array(
				'username_d' => Assign($params['phone']),
			),
		);
		if (empty($params['phoneaes']) == FALSE) {
			$condition['filter']['username'] = Assign($params['phoneaes']);
		}

		$record = StorageFindOne($condition);
		if (is_array($record) and empty($record) == FALSE) {
			$result['msg'] = '该号码已经被注册！';
			die(JsonEncode($result));
		}

		$params['message'] = SMS_ACTION_BANDINGS . $chars;
		break;

	case '2':
		$condition = array(
			'schema' => 'hh_techuser',
			'filter' => array(
				'username_d' => Assign($params['phone']),
			),
		);
		if (empty($params['phoneaes']) == FALSE) {
			$condition['filter']['username'] = Assign($params['phoneaes']);
		}

		$record = StorageFindOne($condition);
		if (is_array($record) == FALSE or empty($record) == TRUE) {
			$result['msg'] = '该号码不存在！';
			die(JsonEncode($result));
		}

		$params['message'] = SMS_ACTION_PASSWORD . $chars;
		break;
}

## 發送短信
if (SMS($params['phone'], $params['message']) == FALSE) {
	$result['msg'] = '发送失败！';
	die(JsonEncode($result));
}

$result = array('code' => '101', 'data' => $chars);

## 統計短信發送
$condition = array(
	'schema' => 'hh_sms_log',
	'filter' => array(
		'createdat' => date('Y-m-d'),
	),
);
$record = StorageFindOne($condition);
if (is_array($record) and empty($record) == FALSE) {
	$fields = array(
		"status{$params['opt']}" => "status{$params['opt']}+1",
	);
	StorageEdit('hh_sms_log', $fields, $condition['filter']);

} else {
	$data = array(
		"status{$params['opt']}" => 1,
		'createdat'              => date('Y-m-d'),
	);
	StorageAdd('hh_sms_log', $data);
}
