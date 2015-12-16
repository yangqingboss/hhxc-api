<?php
// Copyright 2015 The Haohaoxiuche Team Authors. All right reserved.
// Use of this source that is governed by a Apache-style
// license that can be found in the LICENSE file.
//
// 技術版API編號010 獲取歷史問題的詳細信息
//
// @authors hjboss <hongjiangproject@yahoo.com> 2015-12-16#
// @version 1.0.0
// @package hhxc
if (!defined('HHXC')) die('Permission denied');

if (CheckOpenID($params['openid'], $params['uid']) == FALSE) {
	$result['msg'] = MESSAGE_WARING;
} else {
	$condition = array(
		'schema' => 'hh_questions_list',
		'fields' => array('*', ifSQL('nick'), ifSQL('headerimg')),
		'filter' => array(
			'qid' => Assign($params['qid'], 0),
		),
	);

	$recordset = StorageFind($condition);
	if (is_array($recordset) == FALSE or empty($recordset) == TRUE) {
		$result['msg'] = MESSAGE_EMPTY;
	} else {
		$result = array('code' => '101', 'data' => array());

		foreach ($recordset as $index => $row) {
			$result['data'][] = array(
				'userpic'  => $row['h_headerimg'],
				'usernick' => $row['h_nick'],
				'posttime' => $row['pubtime'],
				'message'  => $row['content'],
				'index'    => $index + 1,
			);
		}
	}
}

/**************************************** 輔助函數 ****************************************/
function ifSQL($field) {
	$sql_left  = "SELECT {$field} FROM hh_techuser WHERE id=uid";
	$sql_right = "SELECT {$field} FROM hh_caruser WHERE id=uid";

	return "IF(type=1,({$sql_left}),({$sql_right})) AS h_{$field}";
}

