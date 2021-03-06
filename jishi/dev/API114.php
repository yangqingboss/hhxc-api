<?php
// Copyright 2015 The Haohaoxiuche Team Authors. All right reserved.
// Use of this source that is governed by a Apache-style
// license that can be found in the LICENSE file.
//
// 技術版API編號116 獲取故障類別類別
//
// @authors hjboss <hongjiangproject@yahoo.com> 2015-12-18#
// @version 1.0.0
// @package hhxc
if (!defined('HHXC')) die('Permission denied');

$result = array(
	'code' => '101',
	'msg'  => '',
	'data' => array(
		'怠速不稳', '起步发抖', '冒黑烟',   '启动困难',
		'加速无力', '水温高',   '熄火',     '不制冷',
		'转向沉重', '换档冲击', '行驶跑偏', '灯光不亮',
		'遥控失灵', '喇叭不响', '刹车异响',
	),
);
