<?php
// Copyright 2015 The Haohaoxiuche Team Authors. All right reserved.
// Use of this source that is governed by a Apache-style
// license that can be found in the LICENSE file.
//
// 技術版API編號083 汽修人之置頂數據請求 ##代替052
//
// @authors hjboss <hongjiangproject@yahoo.com> 2015-12-18#
// @version 1.0.0
// @package hhxc
if (!defined('HHXC')) die('Permission denied');

$condition = array(
	'schema' => 'hh_techforum',
	'fields' => array(
		'*',
		'(SELECT nick FROM hh_techuser WHERE id=hh_techqzhi.pubuser)      AS h_nick',
		'(SELECT headerimg FROM hh_techuser WHERE id=hh_techqzhi.pubuser) AS h_headerimg',
		'(SELECT grade FROM hh_techuser WHERE id=hh_techqzhi.pubuser)     AS h_grade',
		'(SELECT COUNT(*) FROM hh_techforum_img WHERE qid=hh_techforum.id AS h_ct',
	),
	'filter' => array(
		'tag'
		'topsite' => array('GT', 0)
	),
);
