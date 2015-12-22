<?php
// Copyright 2015 The Haohaoxiuche Team Authors. All right reserved.
// Use of this source that is governed by a Apache-style
// license that can be found in the LICENSE file.
//
// 技術版API編號081 汽修人之求職數據信息 ##代替018 
//
// @authors hjboss <hongjiangproject@yahoo.com> 2015-12-18#
// @version 1.0.0
// @package hhxc
if (!defined('HHXC')) die('Permission denied');

$condition = array(
	'schema' => 'hh_techqzhi',
	'fields' => array(
		'*',
		'(SELECT nick FROM hh_techuser WHERE id=hh_techqzhi.pubuser) AS h_nick',
		'(SELECT headerimg FROM hh_techuser WHERE id=hh_techqzhi.pubuser) AS h_headerimg',
		'(SELECT grade FROM hh_techuser WHERE id=hh_techqzhi.pubuser) AS h_grade',
	),
	'filter' => array(
		'topsite' => 0,
		'id'      => $params['tid'] ? array('LT', $params['tid']) : array('GT', $params['databasever']),
	),
	'others' => 'ORDER BY id DESC LIMIT 10',
);

switch ($params['type']) {
	case '2':
		$condition['filter']['pubuser'] = Assign($params['uid'], 0);
		break;

	case '3':
		$sql = "(SELECT tid FROM hh_techuser_shoucang WHERE uid='%d' and tag='3' and type='1')";
		$condition['filter']['id'] = array('IN', sprintf($sql, Assign($params['uid'], 0)));
		break;
}

$recordset = StorageFind($condition);
if (is_array($recordset) == FALSE or empty($recordset) == TRUE) {
	$result['msg'] = MESSAGE_EMPTY;
} else {
	$result = array('code' => '101', 'data' => array());

	foreach ($recordset as $index => $row) {
		if ($index == 0) {
			$result['databasever'] = $row['id'];
		}

		if ($params['type'] == '2' and $row['pubuser'] != $params['uid']) {
			continue;
		}

		$buffer = array(
			'uid'        => $row['id'],
			'userpic'    => $row['h_headerimg'],
			'usernick'   => $row['h_nick'],
			'grade'      => $row['h_grade'],
			'posttime'   => $row['pubtime'],
			'level'      => $row['level'],
			'experience' => $row['experience'],
			'city'       => $row['city'],
			'tid'        => $row['tid'],
			'messages'   => $row['replycount'],	
			'collect'    => '0', // 收藏状态
			'mypraise'   => '0', // 我的点赞状态
			'praises'    => '0', // 贴的点赞数量
		);

		$filter_count = array(
			'uid'  => Assign($params['uid'], 0),
			'tid'  => Assign($params['tid'], 0),
			'tag'  => 3,
			'type' => 1,
		);
		if (StorageCount('hh_techuser_shoucang', $filter_count)) {
			$buffer['collect'] = '1';
		}

		if ($params['type'] == '3' and $buffer['collect'] == '0') {
			continue;
		}

		if (StorageCount('hh_techuser_dianzan', $filter_count)) {
			$buffer['mypraise'] = '1';
		}

		$filter_total = array(
			'tid'  => Assign($params['tid'], 0),
			'tag'  => 3,
			'type' => 1,
		);
		$buffer['praises'] = StorageCount('hh_techuser_dianzan', $filter_total);

		$result['data'][] = $buffer;
	}
}

