<?php
// Copyright 2015 The Haohaoxiuche Team Authors. All right reserved.
// Use of this source that is governed by a Apache-style
// license that can be found in the LICENSE file.
//
// 技術版API編號091 汽修人之招聘數據 ##參考081
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
		'(SELECT type FROM hh_techuser WHERE id=hh_zhaopin.ofuser)      AS h_official',
		'(SELECT rank FROM hh_techuser WHERE id=hh_zhaopin.ofuser)      AS h_rank',
		'(SELECT identified FROM hh_techuser WHERE id=hh_zhaopin.ofuser) AS h_identified',
		'(SELECT title FROM hh_rank WHERE dengji=(SELECT rankname FROM hh_techuser WHERE id=hh_zhaopin.ofuser)) AS h_rankname',

	),
	'filter' => array(
		'id'        => array('LT', Assign($params['tid'], 0)),
		'zhuangtai' => array('GT', 0),
		'topsite'   => 0,
	),
	'others' => 'ORDER BY createdat DESC LIMIT 10',
);
if (empty($params['tid']) == TRUE) {
	$condition['filter']['id'] = array('GT', Assign($params['databasever'], 0));
}
switch ($params['type']) {
case '2':
	$condition['filter']['ofuser'] = Assign($param['uid'], 0);
	break;

case '3':
	$tag_buffer = 4;
	$sql = "(SELECT tid FROM hh_techuser_shoucang WHERE uid='%d' AND tag='%d' AND type='1')";
	$condition['filter']['id'] = array('IN', sprintf($sql, $params['uid'], $tag_buffer));
	break;
}

$recordset = StorageFind($condition);
if (is_array($recordset) == FALSE or empty($recordset) == TRUE) {
	$result['msg'] = MESSAGE_EMPTY;
} else {
	$result = array('code' => '101', 'data' => array());

	foreach ($recordset as $number => $row) {
		if ($params['type'] == '2' and $row['ofuser'] != $params['uid']) {
			continue;
		}

		$buffer = array(
			'uid'        => Assign($row['ofuser'], 0),
			'userpic'    => Assign($row['h_headerimg']),
			'usernick'   => Assign($row['h_nick']),
			'grade'      => Assign($row['h_grade']),
			'posttime'   => Assign($row['createdat']),
			'job'        => Assign($row['job']),
			'salary'     => Assign($row['salary']),
			'headcount'  => Assign($row['headcount'], 0),
			'city'       => Assign($row['city']),
			'tid'        => Assign($row['id'], 0),
			'messages'   => Assign($row['replycount'], 0),

			'collect'    => '0', // 收藏状态
			'mypraise'   => '0', // 我的点赞状态
			'praises'    => '0', // 贴的点赞数量

			## 兼容字段
			'official'   => Assign($row['h_official'], 0),
			'identified' => Assign($row['h_identified'], 0),
			'rank'       => Assign($row['h_rank'], 0),
			'rankname'   => Assign($row['h_rankname']),
		);

		## 收藏和點贊統計
		$filter = array(
			'uid'  => $buffer['uid'],
			'tid'  => $buffer['tid'],
			'tag'  => 4,
			'type' => 1,
		);
		if (StorageCount('hh_techuser_shoucang', $filter)) {
			$buffer['collect'] = '1';
		}

		$filter['touid'] = 0;
		if (StorageCount('hh_techuser_dianzan', $filter)) {
			$buffer['mypraise'] = '1';
		}

		$filter = array(
			'tid'   => $buffer['tid'],
			'tag'   => 4,
			'type'  => 1,
			'touid' => 0,
		);
		$buffer['praises'] = StorageCount('hh_techuser_dianzan', $filter);

		$result['data'][] = $buffer;
	}
}
