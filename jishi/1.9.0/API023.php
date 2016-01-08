<?php
// Copyright 2015 The Haohaoxiuche Team Authors. All right reserved.
// Use of this source that is governed by a Apache-style
// license that can be found in the LICENSE file.
//
// 技術版API編號023 查現象之獲取故障點詳細信息 ##已取消 新接口055
//
// @authors hjboss <hongjiangproject@yahoo.com> 2015-12-17#
// @version 1.0.0
// @package hhxc
if (!defined('HHXC')) die('Permission denied');

$condition = array(
	'schema' => array('car_fault', 'car_parts'),
	'fields' => array(
		'*', 
		't0.title AS ztitle',
		't1.title AS ftitle',
	),
	'filter' => array(
		't0.ofpart' => 't1.id',
		't0.id'     => Assign($params['fid'], 0),
	),
);

$recordset = StorageFind($condition);
if (is_array($condition) == FALSE or empty($condition) == TRUE) {
	$result['msg'] = MESSAGE_EMPTY;
} else {
	$result = array('code' => '101', 'data' => array());

	foreach ($recordset as $index => $row) {
		$buffer = array(
			'title'    => fmtstr($row['ztitle']),
			'faultrat' => fmtstr($row['faultrat']),
			'ftitle'   => fmtstr($row['ftitle']),
			'baike'    => fmtstr($row['baike']),
			'solurat'  => fmtstr($row['solurat']),
			'fangfa'   => array(),
		);

		$condition_sub = array(
			'schema' => array('car_fault_diag', 'car_diag'),
			'fields' => array('t1.maintext AS maintext'),
			'filter' => array(
				't0.ofdiag'  => 't1.id',
				't0.offault' => Assign($params['fid'], 0),
			),
		);

		$buf = FindOne($condition_sub);
		if (is_array($buf) and empty($buf) == FALSE) {
			$buffer['fangfa'][] = array(
				'maintext' => fmtstr($row['maintext']),
		}

		$result['data'][] = $buffer;
	}
}

