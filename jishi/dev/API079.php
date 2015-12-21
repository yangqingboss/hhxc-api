<?php
// Copyright 2015 The Haohaoxiuche Team Authors. All right reserved.
// Use of this source that is governed by a Apache-style
// license that can be found in the LICENSE file.
//
// 技術版API編號079 汽修人之用戶數據信息 ##代替011 
//
// @authors hjboss <hongjiangproject@yahoo.com> 2015-12-18#
// @version 1.0.0
// @package hhxc
if (!defined('HHXC')) die('Permission denied');

$condition = array(
	'schema' => 'hh_techforum',
	'fields' => array(
		'pubuser',
		'(SELECT nick FROM hh_techuser WHERE id=hh_techforum.pubuser) AS h_nick',
		'pubtime',
		'title',
		'content',
		'replycount',
		'id',
		'anonymous',
		'(SELECT headerimg FROM hh_techuser WHERE id=hh_techforum.pubuser) AS h_headerimg',
		'(SELECT grade FROM hh_techuser WHERE id=hh_techforum.pubuser)     AS h_grade',
		'(SELECT COUNT(*) FROM hh_techforum_img WHERE qid=hh_techforum.id) AS h_img_total',
	),
	'where' => array(
		'topsite' => 0,
		'type'    => Assign($params['tag'], 0),
	),
	'others' => 'ORDER BY id DESC LIMIT 10',
);

if (empty($params['tid'])) {
	$condition['filter']['id'] = array('GT', Assign($params['databasever'], 0));
} else {
	$condition['filter']['id'] = array('LT', Assign($params['tid'], 0));
}

switch ($params['type']) {
case '2':
	$condition['filter']['pubuser'] = Assign($params['uid'], 0);
	break;

case '3':
	$condition_sub = array(
		'schema' => 'hh_techuser_shoucang',
		'fields' => array('tid'),
		'filter' => array(
			'uid'  => Assign($params['uid'], 0),
			'tag'  => Assign($params['tag'], 0),
			'type' => 1,
		),
	);
	$condition['filter']['id'] = array('IN', '(' . SQLSub($condition_sub) . ')');
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
			'uid'       => $row['pubuser'],
			'userpic'   => $row['h_headerimg'],
			'usernick'  => $row['h_nick'],
			'grade'     => $row['h_grade'],
			'posttime'  => $row['pubtime'],
			'title'     => $row['title'],
			'context'   => $row['content'],
			'messages'  => $row['replycount'],
			'tid'       => $row['id'],
			'anonymous' => $row['anonymous'],
			'medias'    => $row['h_img_total'],
			'mdata'     => array(),
				
			'collect'   => '0', // 收藏状态
			'mypraise'  => '0', // 我的点赞状态
			'praises'   => '0', // 贴的点赞数量
		);

		$filter_count = array(
			'uid'  => Assign($params['uid'],  0), 
			'tag'  => Assign($params['tag'],  0),
			'tid'  => Assign($params['tid'],  0),
			'type' => Assign($params['type'], 0),
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
			'tag'  => Assign($params['tag'],  0),
			'tid'  => Assign($params['tid'],  0),
			'type' => Assign($params['type'], 0),
		);
		$buffer['praises'] = StorageCount('hh_techuser_dianzan', $filter_total);

		$condition_sub = array(
			'schema' => 'hh_techforum_img',
			'filter' => array(
				'qid' => Assign($row['id'], 0),
			),
		);
		$buf = StorageFind($condition_sub);
		if (is_array($buf) and empty($buf) == FALSE) {
			foreach ($buf as $index => $row_img) {
				$buffer['mdata'][] = array(
					'mid'   => $row_img['id'],
					'type'  => '0',
					'mname' => 'image' . ($index + 1),
					'mpic'  => "{$row_img['id']}_s.png",
					'url'   => '',
				);
			}
		}

		$result['data'][] = $buffer;
	}
}

