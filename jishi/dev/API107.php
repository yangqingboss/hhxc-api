<?php
// Copyright 2015 The Haohaoxiuche Team Authors. All right reserved.
// Use of this source that is governed by a Apache-style
// license that can be found in the LICENSE file.
//
// 技術版API編號107 獲取案例跟貼（服務器頁面Ajax專用）
//
// @authors hjboss <hongjiangproject@yahoo.com> 2015-12-18#
// @version 1.0.0
// @package hhxc
if (!defined('HHXC')) die('Permission denied');

$result = array('code' => '101', 'data' => array());

$condition = array(
	'schema' => 'hh_anli_thread',
	'filter' => array(
		'cid' => (empty($_REQUEST['cid']) ? Assign($params['cid'], 0) : $_REQUEST['cid']),
	),
	'others' => 'ORDER BY id DESC',
	'charset' => TRUE, // 兼容老版數據
);
$recordset = StorageFind($condition);
if (is_array($recordset) and empty($recordset) == FALSE) {
	foreach ($recordset as $index => $row) {
		$record = StorageFindID('hh_techuser', $row['uid']);
		
		$result['data'][] = array(
			'uid'     => Assign($row['uid'], 0),
			'nick'    => Assign($record['nick'], NICK_DEFAULT),
			'image'   => empty($record['headerimg']) ? ICON_DEFAULT : ICON_PATH . $record['headerimg'],
			'potimes' => $row['createdat'],
			'context' => $row['content'],
			'cid'     => $row['cid'],
		);
	}
}

