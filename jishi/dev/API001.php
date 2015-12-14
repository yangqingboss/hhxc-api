<?php
// Copyright 2015 The Haohaoxiuche Team Authors. All right reserved.
// Use of this source that is governed by a Apache-style
// license that can be found in the LICENSE file.
//
// 技術版API編號001 用戶首次安裝軟件提交設備ID
//
// @authors hjboss <hongjiangproject@yahoo.com> 2015-12-14#
// @version 1.0.0
// @package hhxc
if (!defined('HHXC')) die('Permission denied');

$record = array(
	'deviceid'  => Assign($params['deviceid']),
	'type'      => strtoupper(Assign($params['device'])),
	'createdat' => 'NOW()',
);

if (StorageAdd('hh_device', $record)) {
	$result = array('code' => '101', 'msg' => MESSAGE_SUCCESS);
}
