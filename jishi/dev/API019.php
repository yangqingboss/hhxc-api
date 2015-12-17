<?php
// Copyright 2015 The Haohaoxiuche Team Authors. All right reserved.
// Use of this source that is governed by a Apache-style
// license that can be found in the LICENSE file.
//
// 技術版API編號019 汽修人之獲取求職主題詳情
//
// @authors hjboss <hongjiangproject@yahoo.com> 2015-12-17#
// @version 1.0.0
// @package hhxc
if (!defined('HHXC')) die('Permission denied');

$record = StorageFindID('hh_techqzhi', Assign($params['tid'], 0));
if (is_array($record) == FALSE or empty($record) == TRUE) {
	$result['msg'] = MESSAGE_EMPTY;
} else {
	$result = array('code' => '101', 'data' => array());

	$buf = array(
		'cars'      => $record['cars'],
		'job'       => $record['job'],
		'introduce' => $record['introduce'],
		'messages'  => array(),
	);
	
	$condition = array(
		'schema' => 'hh_techqzhi_list',
		'fields' => array(
			'*',
			'(SELECT nick FROM hh_techuser WHERE id=pubuser) AS h_nick',
			'(SELECT headerimg FROM hh_techuser WHERE id=pubuser) AS h_headerimg',
		),
		'filter' => array(
			'tid' => Assign($params['tid'], 0),
			'no'  => array('GT', Assign($params['index'], 0)),
		),
	);
	$recordset = StorageFind($condition);
	if (is_array($recordset) and empty($recordset) == FALSE) {
		foreach ($recordset as $index => $row) {
			$buf['messages'][] = array(
				'uid'      => $row['pubuser'],
				'userpic'  => $row['h_headerimg'],
				'usernick' => $row['h_nick'],
				'posttime' => $row['pubtime'],
				'content'  => $row['content'],
				'index'    => $row['no'],
			);
		}
	}

	$result['data'][] = $buf;
}


