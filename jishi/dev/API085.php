<?php
// Copyright 2015 The Haohaoxiuche Team Authors. All right reserved.
// Use of this source that is governed by a Apache-style
// license that can be found in the LICENSE file.
//
// 技術版API編號085 汽修人之信息盒子-我的貼 ##代替069
//
// @authors hjboss <hongjiangproject@yahoo.com> 2015-12-18#
// @version 1.0.0
// @package hhxc
if (!defined('HHXC')) die('Permission denied');

if (CheckOpenID($params['openid'], $params['uid']) == FALSE) {
	$result['msg'] = MESSAGE_WARNING;
} else {
	$result = array('code' => '101', 'data' => array());

	$condition = array();
	if ($params['tag'] == '1' or $params['tag'] == '2') {
		$condition = array(
			'schema' => 'hh_techforum',
			'fields' => array(
				'*',
				"(SELECT COUNT(*) FROM hh_techforum_img WHERE qid=hh_techforum.id) AS h_ct",
				"(SELECT grade FROM hh_techuser WHERE id=pubuser) AS h_grade",

				'(SELECT type FROM hh_techuser WHERE id=pubuser)      AS h_official',
				'(SELECT rank FROM hh_techuser WHERE id=pubuser)      AS h_rank',
				'(SELECT identified FROM hh_techuser WHERE id=pubuser) AS h_identified',
				'(SELECT title FROM hh_rank WHERE dengji=(SELECT rankname FROM hh_techuser WHERE id=pubuser)) AS h_rankname',
			),
			'filter' => array(
				'pubuser' => Assign($params['uid'], 0),
				'type'    => Assign($params['tag'], 0),
			),
			'others' => 'ORDER BY id DESC',
			'column' => array(
				'posttime' => 'pubtime',
				'title'    => 'title',
				'context'  => 'content',
				'tid'      => 'id',
				'messages' => 'replycount',
				'newreply' => 'isnewmsg',
				'grade'    => 'h_grade',
				'medias'   => 'h_ct',

				## 兼容字段
				'official'   => 'h_official',
				'identified' => 'h_identified',
				'rank'       => 'h_rank',
				'rankname'   => 'h_rankname',
				'reward'     => 'rewarded',

			),
		);
		$condition['filter']['id'] = array('LT', Assign($params['tid'], 0));
		if (empty($params['tid'])) {
			$condition['filter']['id'] = array('GT', Assign($params['databasever'], 0));
		}
		switch ($params['type']) {
		case '2':
			$condition['filter']['pubuser'] = Assign($params['uid'], 0);
			break;

		case '3':
			$sql = "(SELECT tid FROM hh_techuser_shoucang WHERE uid='%d' AND tag='%d' AND type='1')";
			$condition['filter']['id'] = array('IN', sprintf($sql, $params['id'], $params['tag']));
			break;
		}

	} else if ($params['tag'] == '3') {
		$condition = array(
			'schema' => 'hh_techqzhi',
			'fields' => array(
				'*',

				'(SELECT type FROM hh_techuser WHERE id=pubuser)      AS h_official',
				'(SELECT rank FROM hh_techuser WHERE id=pubuser)      AS h_rank',
				'(SELECT identified FROM hh_techuser WHERE id=pubuser) AS h_identified',
				'(SELECT title FROM hh_rank WHERE dengji=(SELECT rankname FROM hh_techuser WHERE id=pubuser)) AS h_rankname',

			),
			'filter' => array(
				'pubuser' => Assign($params['uid'], 0),
			),
			'others' => 'ORDER BY id DESC',
			'column' => array(
				'posttime'   => 'pubtime',
				'level'      => 'level',
				'experience' => 'experience',
				'city'       => 'city',
				'tid'        => 'id',
				'messages'   => 'replycount',
				'newreply'   => 'isnewmsg',

				## 兼容字段
				'official'   => 'h_official',
				'identified' => 'h_identified',
				'rank'       => 'h_rank',
				'rankname'   => 'h_rankname',
				'reward'     => 'rewarded',
			),
		);
		$condition['filter']['id'] = array('LT', Assign($params['tid'], 0));
		if (empty($params['tid'])) {
			$condition['filter']['id'] = array('GT', Assign($params['databasever'], 0));
		}
		switch ($params['type']) {
		case '2':
			$condition['filter']['pubuser'] = Assign($params['uid'], 0);
			break;

		case '3':
			$sql = "(SELECT tid FROM hh_techuser_shoucang WHERE uid='%d' AND tag='%d' AND type='1')";
			$condition['filter']['id'] = array('IN', sprintf($sql, $params['id'], $params['tag']));
			break;
		}

	} else if ($params['tag'] == '4') {
		$condition = array(
			'schema' => 'hh_zhaopin',
			'fields' => array(
				'*',

				'(SELECT type FROM hh_techuser WHERE id=ofuser)      AS h_official',
				'(SELECT rank FROM hh_techuser WHERE id=ofuser)      AS h_rank',
				'(SELECT identified FROM hh_techuser WHERE id=ofuser) AS h_identified',
				'(SELECT title FROM hh_rank WHERE dengji=(SELECT rankname FROM hh_techuser WHERE id=ofuser)) AS h_rankname',

			),
			'filter' => array(
				'ofuser' => Assign($params['uid'], 0),
			),
			'others' => 'ORDER BY id DESC',
			'column' => array(
				'posttime'  => 'createdat',
				'job'       => 'job',
				'salary'    => 'salary',
				'headcount' => 'headcount',
				'city'      => 'city',
				'tid'       => 'id',
				'messages'  => 'replycount',
				'newreply'  => 'isnewmsg',
				'ischecked' => 'zhuangtai',

				## 兼容字段
				'official'   => 'h_official',
				'identified' => 'h_identified',
				'rank'       => 'h_rank',
				'rankname'   => 'h_rankname',
				'reward'     => 'rewarded',
			),

		);
		$condition['filter']['id'] = array('LT', Assign($params['tid'], 0));
		if (empty($params['tid'])) {
			$condition['filter']['id'] = array('GT', Assign($params['databasever'], 0));
		}
		switch ($params['type']) {
		case '2':
			$condition['filter']['ofuser'] = Assign($params['uid'], 0);
			break;

		case '3':
			$sql = "(SELECT tid FROM hh_techuser_shoucang WHERE uid='%d' AND tag='%d' AND type='1')";
			$condition['filter']['id'] = array('IN', sprintf($sql, $params['id'], $params['tag']));
			break;
		}
		
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
			
			foreach ($condition['column'] as $key => $val) {
				$buffer[$key] = Assign($row[$val], 0);
			}

			$filter_count = array(
				'uid'  => Assign($params['uid'], 0),
				'tid'  => Assign($params['tid'], 0),
				'tag'  => Assign($params['tag'], 0),
				'type' => '1',
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
				'tag'  => Assign($params['tag'], 0),
				'type' => '1',
			);
			$buffer['praises'] = StorageCount('hh_techuser_dianzan', $filter_total);

			## 獲取圖片信息
			if ($params['tag'] == '1' or $params['tag'] == '2') {
				$buffer['mdata'] = array();

				$condition_sub = array(
					'schema' => 'hh_techforum_img',
					'fields' => array('id'),
					'filter' => array(
						'qid' => Assign($row['id'], 0),
					),
				);
				$buf = StorageFind($condition_sub);
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
			}

			$result['data'][] = $buffer;
		}
	}
}

