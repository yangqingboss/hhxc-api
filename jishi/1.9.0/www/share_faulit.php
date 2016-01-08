<?php
// Copyright 2015 The Haohaoxiuche Team Authors. All right reserved.
// Use of this source that is governed by a Apache-style
// license that can be found in the LICENSE file.
//
// 技術版APP 分享頁面公共頁面
//
// @authors hjboss <hongjiangproject@yahoo.com> 2015-12-14#
// @version 1.0.0
// @package hhxc
define('HHXC', TRUE);
require_once('common.php');

$condition = array(
	'schema' => 'hh_techuser_cxsym',
	'fields' => array(
		'*',
		'(select title from car_symptom where id=ofsymptom) as pheno',
		'(select title from car_fault where id=offault) as h_title',
		'(select baike from car_parts where id=(select ofpart from car_fault where id=hh_techuser_cxsym.offault)) as h_baike',
		'(select title from car_parts where id=(select ofpart from car_fault where id=hh_techuser_cxsym.offault))',
	),
	'filter' => array(
		'id' => $_REQUEST['id'],
	),
);
$cxsym = StorageFindOne($condition);
$title = $cxsym['h_title'];
$content = $cxsym['h_baike'];
include_once('share_common.php');
