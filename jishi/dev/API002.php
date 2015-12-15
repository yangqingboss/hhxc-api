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

$condition = array(
	'schema' => 'hh_dbver',
	'fields' => array('*', 'COUNT(*) AS h_ct'),
	'filter' => array(
		'vername' => 'loopimg',
		'ver'     => array('GT', Assign($params['databasever'], 0)),
	),
);

$record = StorageFindOne($condition);
var_dump($record);
