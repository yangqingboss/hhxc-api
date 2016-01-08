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
$pubuser = $_REQUEST['tag'] == 4 ? 'ofuser' : 'pubuser';
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
$headerimg = '';
if ($record['h_headerimg']) {
	$headerimg = "<img src=\"http://www.haohaoxiuche.com/api/userimg/{$record['h_headerimg']}\" height=\"32\" />";
} else {
	$headerimg = "<img src=\"http://haohaoxiuche.com/css/icon_default.png\" height=\"32\" />";
}
$headerimg .= $record['h_nick'];
switch ($_REQUEST['tag']) {
case '3':
	$title = '我的简历如下：';
	$content = <<<EOD
<table width="100%" class="h-table">
<tr>
	<th><strong>昵称</strong>：</th>
	<td>{$record['h_nick']}</td>
	<th><strong>职称</strong>：</th>
	<td>{$record['level']}</td>
</tr>
<tr>
	<th class="h-long"><strong>工种/年限</strong>：</th>
	<td>{$record['job']}/{$record['experience']}</td>
	<th><strong>城市</strong>：</th>
	<td>{$record['city']}</td>
</tr>
<tr>
	<th class="h-long"><strong>擅长品牌</strong>：</th>
	<td colspan="3">{$record['cars']}</td>
</tr>
</table>
EOD;
	break;

case '4':
	$title = '招聘信息如下：';
	$content = <<<EOD
<table width="100%" class="h-table">
<tr>
	<th><strong>岗位</strong>：</th>
	<td colspan="3">{$record['job']}</td>
</tr>
<tr>
	<th><strong>人数</strong>：</th>
	<td colspan="3">{$record['headcount']}</td>
</tr>
<tr>
	<th><strong>待遇</strong>：</th>
	<td colspan="3">{$record['salary']}</td>
</tr>
<tr>
	<th><strong>城市</strong>：</th>
	<td colspan="3">{$record['city']}</td>
</tr>
<tr>
	<th class="h-long"><strong>主营业务</strong>：</th>
	<td colspan="3">{$record['business']}</td>
</tr>
<tr>
	<th class="h-long"><strong>公司规模</strong>：</th>
	<td colspan="3">{$record['scale']}</td>
</tr>
<tr>
	<th class="h-long"><strong>联系方式</strong>：</th>
	<td colspan="3">{$record['contactinfo']}</td>
</tr>
<tr>
	<th class="h-long"><strong>福利待遇</strong>：</th>
	<td colspan="3">{$record['boon']}</td>
</tr>
<tr>
	<th class="h-long"><strong>公司名称</strong>：</th>
	<td colspan="3">{$record['name']}</td>
</tr>
<tr>
	<th class="h-long"><strong>公司地址</strong>：</th>
	<td colspan="3">{$record['location']}</td>
</tr>
<tr>
	<th class="h-long"><strong>其他信息</strong>：</th>
	<td colspan="3">{$record['etc']}</td>
</tr>
</table>
EOD;
	break;

default:
	$title     = Assign($record['title'], 0);
	$content   = str_replace('\n', '<br />', Assign($record['content']));
}


include_once('share_common.php');
