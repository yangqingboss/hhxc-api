<?php
// Copyright 2015 The Haohaoxiuche Team Authors. All right reserved.
// Use of this source that is governed by a Apache-style
// license that can be found in the LICENSE file.
//
// 技術版API編號117 获取关键词列表
//
// @authors hjboss <hongjiangproject@yahoo.com> 2015-12-18#
// @version 1.0.0
// @package hhxc
if (!defined('HHXC')) die('Permission denied');

$result = array(
	'code' => '101',
	'msg'  => '',
	'data' => array(
		array(
			'keywordType' => '1',
			'keywordName' => '手动变速器',
			'data'        => array(
				'柴油机运转无力，加速不良', 
				'换挡时发动机熄火',
				'空调冷气不足，能制冷但不够冷',
				'空调制冷间断，不连续，时有时无',
				'空调系统有噪音',
				'水面经常有油渍',
				'发动机水温过高，超过红线',
			),
		),
	),
);

