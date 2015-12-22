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
		'(SELECT nick FROM hh_techuser WHERE id=hh_techforum.pubuser)      AS h_nick',
		'(SELECT headerimg FROM hh_techuser WHERE id=hh_techforum.pubuser) AS h_headerimg',
		'(SELECT grade FROM hh_techuser WHERE id=hh_techforum.pubuser)     AS h_grade',
		'(SELECT COUNT(*) FROM hh_techforum_img WHERE qid=hh_techforum.id) AS h_ct',
	),
	'filter' => array(
		'type'    => Assign($params['tag'], 0),
		'topsite' => array('GT', 0)
	),
	'others' => 'ORDER BY topsite LIMIT 10',
);
switch ($params['type']) {
case '2':
	$condition['filter']['pubuser'] = Assign($params['uid'], 0);
	break;

case '3':
	$sql = "(SELECT tid FROM hh_techuser_shoucang WHERE uid='%d' AND tag='%d' and type='1')";
	$$condition['filter']['id'] = array('IN', sprintf($sql, $params['uid'], $params['tag']));
	break;
}

$recordset = StorageFind($condition);
if (is_array($recordset) == FALSE or empty($recordset) == TRUE) {
	$result['msg'] = MESSAGE_EMPTY;
} else {
	$result = array('code' => '101', 'data' => array());

	foreach ($recordset as $index => $row) {
		if ($index == 0) {
			$result['databaserver'] = $row['id'];
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
			'medias'    => $row['h_ct'],
			'mdata'     => array(),
			'collect'   => '0',
			'mypraise'  => '0',
			'praises'   => '0',
		);

		$filter_count = array(
			'uid'  => $row['pubuser'],
			'tid'  => $row['id'],
			'tag'  => Assign($params['tag'], 0),
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
			'tid'  => $row['id'],
			'tag'  => Assign($params['tag'], 0),
			'type' => 1,
		);
		$buffer['praises'] = StorageCount('hh_techuser_dianzan', $filter_total);

		## 獲取圖片信息
		$condition_buf = array(
			'schema' => 'hh_techforum_img',
			'fields' => array('id'),
			'filter' => array(
				'qid' => $row['id'],
			),
		);
		$buf = StorageFind($condition_buf);
		if (is_array($buf) and empty($buf) == FALSE) {
			foreach ($buf as $number => $row_img) {
				$buffer['mdata'][] = array(
					'mid'   => $row_img['id'],
					'type'  => '0',
					'mname' => 'image' . ($number + 1),
					'mpic'  => "{$row_img['id']}_s.png",
					'url'   => '',
				);
			}
		}

		$result['data'][] = $buffer;
	}
}

