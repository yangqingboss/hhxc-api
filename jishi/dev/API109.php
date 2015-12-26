<?php
// Copyright 2015 The Haohaoxiuche Team Authors. All right reserved.
// Use of this source that is governed by a Apache-style
// license that can be found in the LICENSE file.
//
// 技術版API編號109 獲取認證數據
//
// @authors hjboss <hongjiangproject@yahoo.com> 2015-12-18#
// @version 1.0.0
// @package hhxc
if (!defined('HHXC')) die('Permission denied');

if (CheckOpenID($params['openid'], $params['uid']) == FALSE) {
	$result['msg'] = MESSAGE_WARNING;
} else {
	$condition = array(
		'schema' => 'hh_identification',
		'fields' => '*',
		'filter' => array(
			'uid' => Assign($params['uid'], 0),
		),
	);
	$record = StorageFindOne($condition);
	if (is_array($record) == FALSE or empty($record) == TRUE) {
		$result['msg'] = MESSAGE_EMPTY;
	} else {
		$result = array('code' => '101', 'data' => array());

		$images = 0;
		if (empty($record['fileid']) == FALSE) {
			$images += 1；
		}
		if (empty($record['filerz1']) == FALSE) {
			$images += 1;
		}
		if (empty($record['filerz2']) == FALSE) {
			$images += 1;
		}

		$result['data'][] = array(
			'identification' => $record['identification'],
			'name'           => $record['name'],
			'states'         => $record['status'],
			'images'         => $images,
			'fileid'         => $record['fileid'],
			'filerz1'        => $record['filerz1'],
			'filerz2'        => $record['filerz2'],
		);
	}
}
