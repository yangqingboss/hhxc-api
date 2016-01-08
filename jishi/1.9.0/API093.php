<?php
// Copyright 2015 The Haohaoxiuche Team Authors. All right reserved.
// Use of this source that is governed by a Apache-style
// license that can be found in the LICENSE file.
//
// 技術版API編號093 汽修人之招聘置頂數據 ##參考084
//
// @authors hjboss <hongjiangproject@yahoo.com> 2015-12-18#
// @version 1.0.0
// @package hhxc
if (!defined('HHXC')) die('Permission denied');

$condition = array(
	'schema' => 'hh_zhaopin',
	'fields' => array(
		'*',
		'(select nick from hh_techuser where id=hh_zhaopin.ofuser) AS h_nick',
		'(select headerimg from hh_techuser where id=hh_zhaopin.ofuser) AS h_headerimg',
		'(select grade from hh_techuser where id=hh_zhaopin.ofuser) AS h_grade',

		'(SELECT type FROM hh_techuser WHERE id=hh_zhaopin.ofuser)       AS h_official',
		'(SELECT rank FROM hh_techuser WHERE id=hh_zhaopin.ofuser)       AS h_rank',
		'(SELECT identified FROM hh_techuser WHERE id=hh_zhaopin.ofuser) AS h_identified',
		'(SELECT title FROM hh_rank WHERE dengji=(SELECT rankname FROM hh_techuser WHERE id=hh_zhaopin.ofuser)) AS h_rankname',
	),
	'filter' => array(
		'topsite' => array('GT', 0),
	),
	'others' => 'ORDER BY createdat DESC LIMIT 10',
);
switch ($params['type']) {
case '2':
	$condition['filter']['ofuser'] = Assign($params['uid'], 0);
	break;

case '3':
	$tag_buffer = 4;
	$sql = "(SELECT tid FROM hh_techuser_shoucang WHERE uid='%d' AND tag='%s' AND type='1')";
	$condition['filter']['id'] = array('IN', sprintf($sql, $params['uid'], $tag_buffer));
	break;
}
$recordset = StorageFind($condition);
if (is_array($recordset) == FALSE or empty($recordset) == TRUE) {
	$result['msg'] = MESSAGE_EMPTY;
} else {
	$result = array('code' => '101', 'data' => array());

	foreach ($result as $index => $row) {
		if ($index == 0) {
			$result['databaserver'] = $row['id'];
		}

		if ($params['type'] == '2' and $row['pubuser'] != $params['uid']) {
			continue;
		}

		$buffer = array(
			'uid'       => Assign($params['ofuser'], 0),
			'userpic'   => Assign($params['h_headerimg']),
			'usernick'  => Assign($params['h_nick']),
			'grade'     => Assign($params['h_grade']),
			'posttime'  => Assign($params['createdat']),
			'job'       => Assign($params['job']),
			'salary'    => Assign($params['salary']),
			'headcount' => Assign($params['headcount'], 0),
			'city'      => Assign($params['city']),
			'tid'       => Assign($params['id'], 0),
			'messages'  => Assign($params['replycount'], 0),

			'collect'   => '0', // 收藏状态
			'mypraise'  => '0', // 我的点赞状态
			'praises'   => '0', // 贴的点赞数量

			## 兼容字段
			'official'   => Assign($row['h_official'], 0),
			'identified' => Assign($row['h_identified'], 0),
			'rank'       => Assign($row['h_rank'], 0),
			'rankname'   => Assign($row['h_rankname']),
		);

		$filter_count = array(
			'uid'  => Assign($params['uid'],  0), 
			'tag'  => 4,
			'tid'  => Assign($buffer['tid'],  0),
			'type' => Assign($params['type'], 0),
		);
		if (StorageCount('hh_techuser_shoucang', $filter_count)) {
			$buffer['collect'] = '1';
		}

		if ($params['type'] == '3' and $buffer['collect'] == '0') {
			continue;
		}

		$filter_count['touid'] = 0;
		if (StorageCount('hh_techuser_dianzan', $filter_count)) {
			$buffer['mypraise'] = '1';
		}

		$filter_total = array(
			'tag'  => 4,
			'tid'  => Assign($buffer['tid'],  0),
			'type' => Assign($params['type'], 0),
			'touid' => 0,
		);
		$buffer['praises'] = StorageCount('hh_techuser_dianzan', $filter_total);

		$result['data'][] = $buffer;
	}
}
	
