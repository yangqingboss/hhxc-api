<?php
// Copyright 2015 The Haohaoxiuche Team Authors. All right reserved.
// Use of this source that is governed by a Apache-style
// license that can be found in the LICENSE file.
//
// 技術版API編號002 獲取汽修人問答之提問信息
//
// @authors hjboss <hongjiangproject@yahoo.com> 2015-12-14#
// @version 1.0.0
// @package hhxc
if (!defined('HHXC')) die('Permission denied');

if (CheckOpenID($params['openid'], $params['uid']) == FALSE) {
	$result['msg'] = MESSAGE_WARING;
} else {
	$condition = array(
		'schema' => array('hh_questions AS t0', 'hh_techuser as t1'),
		'fields' => array('*', 't0.id AS qid', 't1.id AS uid'),
		'filter' => array(
			't0.pubuser'   => 't1.id',
			't0.zhuangtai' => 0,
		),
		'others' => 'ORDER BY t0.id DESC LIMIT 10',
	);
}
