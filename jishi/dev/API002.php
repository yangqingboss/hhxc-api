<?php
// Copyright 2015 The Haohaoxiuche Team Authors. All right reserved.
// Use of this source that is governed by a Apache-style
// license that can be found in the LICENSE file.
//
// 技術版API編號002 獲取輪播圖數據信息
//
// @authors hjboss <hongjiangproject@yahoo.com> 2015-12-14#
// @version 1.0.0
// @package hhxc
if (!defined('HHXC')) die('Permission denied');

$filter = array(
	'vername' => 'loopimg', 
	'ver'     => array('GT', Assign($params['databasever'], '0'))
);

$record = StorageQueryOne('hh_dbver', '*', $filter);
var_dump($record);die();
if (is_array($record) and intval($record['h_ct']) > 0) {
	$result = array('code' => '101', 'databasever' => $record['ver'], 'data' => array());

	$recordset = StorageQuery('hh_loopimg', '*', array('zhuangtai' => 1), 'ORDER BY site ASC');
	if (is_array($recordset)) {
		foreach ($recordset as $index => $row) {
			$result['data'][] = array(
				'imagename' => Assign($row['filename']),
				'url'       => Assign($row['url']) . '&m=mobile',
			);
		}
	}
}

