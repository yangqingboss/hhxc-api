<?php
// Copyright 2015 The Haohaoxiuche Team Authors. All right reserved.
// Use of this source that is governed by a Apache-style
// license that can be found in the LICENSE file.
//
// 技術版API編號089 檢查更新 ##代替039
//
// @authors hjboss <hongjiangproject@yahoo.com> 2015-12-18#
// @version 1.0.0
// @package hhxc
if (!defined('HHXC')) die('Permission denied');

if (CheckOpenID($params['openid'], $params['uid']) == FALSE) {
	$result['msg'] = MESSAGE_WARNING;
} else {
	$condition = array(
		'schema' => 'hh_dbver',
		'fields' => array('ver', 'remark'),
		'filter' => array(
			'vername' => 'vername_' . Assign($params['tag']),
		),
	);

	$recordset = StorageFind($condition);
	if (is_array($recordset) or empty($recordset) == TRUE) {
		$result['msg'] = MESSAGE_EMPTY;
	} else {
		if ($recordset[0]['ver'] > Assign($params['version'], 0)) {
			$result = array('code' => '101', 'data' => array());
			
			$result['data'][] = array(
				'version' => $recordset[0]['ver'],
				'url'     => $recordset[0]['url'],
			);
		}
	}
}

