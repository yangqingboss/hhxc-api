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

$schemas = array('1' => 'hh_techforum', '2' => 'hh_techforum', '3' => 'hh_techqzhi', '4' => 'hh_zhaopin');
$pubuser = $_REQUEST['tag'] == 4 ? '' : 'pubuser';
$condition = array(
	'schema' => array($schemas[Assign($_REQUEST['tag'], 0)]),
	'fields' => array(
		'*',
		"(SELECT headerimg from hh_techuser where id=t0.{$pubuser}) AS h_headerimg",
		"(SELECT nick from hh_techuser where id=t0.{$pubuser}) AS h_nick",
	),
	'filter' => array(
		'id' => Assign($_REQUEST['tid'], 0),
	),
);

## 查看分享內容
$record = StorageFindOne($condition);

$headerimg = <<<EOD
<img src="http://www.haohaoxiuche.com/api/userimg/{$record['h_headerimg']}" height="24" />
{$record['h_nick']}
EOD;

switch ($_REQUEST['tag']) {
case '3':
case '4':
default:
	$title     = Assign($record['title'], 0);
	$content   = str_replace('\n', '<br />', Assign($record['content']));
}


include_once('share_common.php');
