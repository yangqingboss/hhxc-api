<?php
// Copyright 2015 The Haohaoxiuche Team Authors. All right reserved.
// Use of this source that is governed by a Apache-style
// license that can be found in the LICENSE file.
//
// 技術版API編號088 熱搜詞
//
// @authors hjboss <hongjiangproject@yahoo.com> 2015-12-18#
// @version 1.0.0
// @package hhxc
if (!defined('HHXC')) die('Permission denied');

$condition = array(
	'schema' => 'hh_dbver',
	'fields' => array(
		'COUNT(*)',
		'ver',
	),
	'filter' => array(
		'vername' => 'app_' . Assign($params['tag']),
		'ver'     => array('GT', Assign($params['databasever'], 0)),
	),
);
$recordset = StorageFind($condition);
if (is_array($recordset) == FALSE or empty($recordset) == TRUE) {
	$result['msg'] = MESSAGE_EMPTY;
} else {
	$result = array('code' => '101', 'data' => array(), 'messages' => 0);

	if (empty($recordset) == FALSE) {
		$result['databasever'] = $recordset[0]['ver'];
	}

	$condition_sub = array(
		'schema' => 'hh_hotword',
		'fields' => array('id', 'title', 'shuliang'),
		'filter' => array(
			'tag' => Assign($params['tag']),
		),
	);
	$buf = StorageFind($condition_sub);
	if (is_array($buf) and empty($buf) == FALSE) {
		foreach ($buf as $index => $row) {
			$result['data'][] = array(
				'sid'         => $row['id'],
				'scontent'    => $row['title'],
				'probability' => $row['shuliang'],
			);

			$result['messages'] += 1;
		}
	}
}
