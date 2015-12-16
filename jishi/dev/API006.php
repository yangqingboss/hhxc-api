<?php
// Copyright 2015 The Haohaoxiuche Team Authors. All right reserved.
// Use of this source that is governed by a Apache-style
// license that can be found in the LICENSE file.
//
// 技術版API編號006 獲取正在解決問題信息
//
// @authors hjboss <hongjiangproject@yahoo.com> 2015-12-16#
// @version 1.0.0
// @package hhxc
if (!defined('HHXC')) die('Permission denied');

if (CheckOpenID($params['openid'], $params['uid']) == FALSE) {
	$result['msg'] = MESSAGE_WARING;
} else {
	$condition = array(
		'schema' => array('hh_questions', 'hh_techuser'),
		'fields' => array('*', 't0.id AS qid', 't1.id AS uid'),
		'filter' => array(
			't0.pubuser'   => 't1.id',
			't0.zhuangtai' => 1,
			't0.lockby'    => Assign($params['uid'], 0),
		),
	);

	$record = StorageFindOne($condition);
	if (is_array($record) and empty($record) == TRUE) {
		$result['msg'] = MESSAGE_EMPTY;
	} else {
		$result = array(
			'code' => '101',
			'data' => array(
				'uid'        => $record['uid'],
				'userpic'    => $record['headerimg'],
				'usernick'   => $record['nick'],
				'difficulty' => $record['difficulty'],
				'posttime'   => $record['pubtime'],
				'question'   => $record['question'],
				'qid'        => $record['qid'],
				'report'     => array(),
				'answers'    => array(),
			),
		);

		$condition_s = array(
			'schema' => 'hh_questions_list',
			'fields' => array('*', ifSQL('nick'), ifSQL('headerimg')),
			'filter' => array(
				'qid'       => $recod['qid'],
				'zhuangtai' => 0,
			),
		);

		$buf = StorageFind($condition_s);
		if (is_array($buf) and empty($buf) == FALSE) {
			foreach ($buf as $index => $row) {
				$result['data'][0][] = array(
					'type'     => $row['type'],
					'userpic'  => $row['h_headerimg'],
					'usernick' => $row['h_nick'],
					'posttime' => $row['posttime'],
					'message'  => $row['content'],
					'index'    => $row['no'],
				);
			}
		}
	}	
}

/**************************************** 輔助函數 ****************************************/
function ifSQL($field) {
	$sql_left  = "SELECT {$field} FROM hh_techuser WHERE id=uid";
	$sql_right = "SELECT {$field} FROM hh_caruser WHERE id=uid";

	return "IF(type=1,({$sql_left}),({$sql_right})) AS h_{$field}";
}
