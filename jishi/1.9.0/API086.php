<?php
// Copyright 2015 The Haohaoxiuche Team Authors. All right reserved.
// Use of this source that is governed by a Apache-style
// license that can be found in the LICENSE file.
//
// 技術版API編號086 汽修人之信息盒子-@我的數據 ##代替070
//
// @authors hjboss <hongjiangproject@yahoo.com> 2015-12-18#
// @version 1.0.0
// @package hhxc
if (!defined('HHXC')) die('Permission denied');

if (CheckOpenID($params['openid'], $params['uid']) == FALSE) {
	$result['msg'] = MESSAGE_WARNING;
} else {
	$result = array('code' => '101', 'data' => array());

	$condition = $edit = array();
	if ($params['tag'] == '1' or $params['tag'] == '2') {
		$condition = array(
			'schema' => array('hh_techforum_list', 'hh_techforum', 'hh_techuser'),
			'fields' => array(
				't2.id AS h_ouid',
				't2.headerimg',
				't2.nick',
				't1.anonymous',
				't1.pubtime',
				't1.title',
				't1.id AS h_tid',
				't1.replycount',
				't0.isnewat',
				't0.pubuser',
				't2.nick',
				't0.content AS h_rcontent',
				'(SELECT headerimg FROM hh_techuser WHERE id=t0.pubuser)    AS h_headerimg',
				't0.pubtime',
				't0.atlist',
				'(SELECT content FROM hh_techforum_list WHERE id=t0.atlist) AS h_content',
				'(SELECT headerimg FROM hh_techuser WHERE id=t0.pubuser)    AS h_headerimg_1',
				'(SELECT nick FROM hh_techuser WHERE id=t0.pubuser)         AS h_nick',
				't0.pubtime AS h_rposttime',
				't1.content AS h_context',
				't2.grade   AS h_ograde',
				'(SELECT grade FROM hh_techuser WHERE id=t0.pubuser)        AS h_grade',

				'(SELECT type FROM hh_techuser WHERE id=t0.pubuser)      AS h_official',
				'(SELECT rank FROM hh_techuser WHERE id=t0.pubuser)      AS h_rank',
				'(SELECT identified FROM hh_techuser WHERE id=t0.pubuser) AS h_identified',
				'(SELECT title FROM hh_rank WHERE dengji=(SELECT rankname FROM hh_techuser WHERE id=t0.pubuser)) AS h_rankname',

			),
			'filter' => array(
				't0.id'      => array('LT', Assign($params['tid'], 0)),
				't0.tid'     => 't1.id',
				't1.pubuser' => 't2.id',
				't0.at'      => Assign($params['uid'], 0),
				't1.type'    => Assign($params['tag'], 0),
			),
			'others' => 'ORDER BY t0.id DESC',
			'column' => array(
				'ouid'      => 'h_ouid',
				'ouserpic'  => 'headerimg',
				'ousernick' => 'nick',
				'ograde'    => 'h_ograde',
				'anonymous' => 'anonymous',
				'posttime'  => 'pubtime',
				'title'     => 'title',
				'tid'       => 'h_tid',
				'messages'  => 'replycount',
				'newreply'  => 'isnewat',
				'ruid'      => 'pubuser',
				'ruserpic'  => 'h_headerimg',
				'rusernick' => 'h_nick',
				'rgrade'    => 'h_grade',
				'rposttime' => 'h_rposttime',
				'rcontext'  => 'h_rcontent',
				'mlistid'   => 'atlist',
				'mcontent'  => 'h_content',
				'context'   => 'h_context',

				## 兼容字段
				'official'   => 'h_official',
				'identified' => 'h_identified',
				'rank'       => 'h_rank',
				'rankname'   => 'h_rankname',
				'reward'     => 'rewarded',
				'rewardata'  => 'reward',

			),
		);
		if (empty($params['tid'])) {
			$condition['filter']['t0.id'] = array('GT', Assign($params['databasever'], 0));
			$condition['others'] = 'ORDER BY t0.id DESC LIMIT 10';
		}
		switch ($params['type']) {
		case '2':
			$condition['filter']['t0.pubuser'] = Assign($params['uid'], 0);
			break;

		case '3':
			$sql = "(SELECT tid FROM hh_techuser_shoucang WHERE uid='%d' AND tag='%d' AND type='1')";
			$condition['filter']['t1.id'] = array('IN', sprintf($sql, $params['uid'], $params['tag']));
			break;
		}

		$edit = array(
			'schema' => 'hh_techforum_list',
			'fields' => array(
				'isnewat' => 0,
				'isnew'   => 0,
			),
			'filter' => array(
				'type' => Assign($params['tag'], 0),
				'at'   => Assign($params['uid'], 0),
			),
		);

	} else if ($params['tag'] == '3') {
		$condition = array(
			'schema' => array('hh_techqzhi_list', 'hh_techqzhi', 'hh_techuser'),
			'fields' => array(
				't2.id AS h_ouid',
				't2.headerimg',
				't2.nick',
				't1.pubtime AS h_posttime',
				't1.level',
				't1.experience',
				't1.id AS h_tid',
				't1.replycount',
				't0.isnewat',
				't0.pubuser AS h_ruid',
				't2.nick',
				't0.content AS h_rcontext',
				'(SELECT headerimg FROM hh_techuser WHERE id=t0.pubuser)   AS h_headerimg',
				't0.pubtime',
				't0.atlist',
				'(SELECT content FROM hh_techqzhi_list WHERE id=t0.atlist) AS h_content',
				'(SELECT headerimg FROM hh_techuser WHERE id=t0.pubuser)   AS h_headerimg_1',
				'(SELECT nick FROM hh_techuser WHERE id=t0.pubuser)        AS h_nick',
				't0.pubtime AS h_rposttime',
				't1.city',
				't2.grade   AS h_ograde',
				'(SELECT grade FROM hh_techuser WHERE id=t0.pubuser)       AS h_grade',

				'(SELECT type FROM hh_techuser WHERE id=t0.pubuser)      AS h_official',
				'(SELECT rank FROM hh_techuser WHERE id=t0.pubuser)      AS h_rank',
				'(SELECT identified FROM hh_techuser WHERE id=t0.pubuser) AS h_identified',
				'(SELECT title FROM hh_rank WHERE dengji=(SELECT rankname FROM hh_techuser WHERE id=t0.pubuser)) AS h_rankname',

			),
			'filter' => array(
				't0.id'      => array('LT', Assign($params['tid'], 0)),
				't0.tid'     => 't1.id',
				't1.pubuser' => 't2.id',
				't0.at'      => Assign($params['uid'], 0),
			),
			'others' => 'ORDER BY t0.id DESC',
			'column' => array(
				'ouid'       => 'h_ouid',
				'ouserpic'   => 'headerimg',
				'ousernick'  => 'nick',
				'ograde'     => 'h_ograde',
				'rgrade'     => 'h_grade',
				'posttime'   => 'h_posttime',
				'level'      => 'level',
				'experience' => 'experience',
				'city'       => 'city',
				'tid'        => 'h_tid',
				'messages'   => 'replycount',
				'newreply'   => 'isnewat',
				'ruid'       => 'h_ruid',
				'ruserpic'   => 'h_headerimg',
				'rusernick'  => 'h_nick',
				'rposttime'  => 'h_rposttime',
				'rcontext'   => 'h_rcontext',
				'mlistid'    => 'atlist',
				'mcontent'   => 'h_content',

				## 兼容字段
				'official'   => 'h_official',
				'identified' => 'h_identified',
				'rank'       => 'h_rank',
				'rankname'   => 'h_rankname',
				'reward'     => 'rewarded',
				'rewardata'  => 'reward',

			),
		);
		if (empty($params['tid'])) {
			$condition['filter']['t0.id'] = array('GT', Assign($params['databasever'], 0));
			$condition['others'] = 'ORDER BY t0.id DESC LIMIT 10';
		}
		switch ($params['type']) {
		case '2':
			$condition['filter']['t0.pubuser'] = Assign($params['uid'], 0);
			break;

		case '3':
			$sql = "(SELECT tid FROM hh_techuser_shoucang WHERE uid='%d' AND tag='%d' AND type='1')";
			$condition['filter']['t0.id'] = array('IN', sprintf($sql, $params['uid'], $params['tag']));
			break;
		}

		$edit = array(
			'schema' => 'hh_techqzhi_list',
			'fields' => array(
				'isnewat' => 0,
				'isnew'   => 0,
			),
			'filter' => array(
				'at' => Assign($params['uid'], 0),
			),
		);

	} else if ($params['tag'] == '4') {
		$condition = array(
			'schema' => array('hh_zhaopin_list', 'hh_zhaopin', 'hh_techuser'),
			'fields' => array(
				't2.id AS h_ouid',
				't2.headerimg',
				't2.nick',
				't1.createdat',
				't1.job',
				't1.salary',
				't1.id AS h_tid',
				't1.replycount',
				't0.isnewat',
				't0.pubuser AS h_ruid',
				't2.nick',
				't0.content AS h_rposttime',
				'(SELECT headerimg FROM hh_techuser WHERE id=t0.pubuser)   AS h_headerimg',
				't0.pubtime',
				't0.atlist',
				'(SELECT content FROM hh_techqzhi_list WHERE id=t0.atlist) AS h_content',
				'(SELECT headerimg FROM hh_techuser WHERE id=t0.pubuser)   AS h_headerimg_1',
				'(SELECT nick FROM hh_techuser WHERE id=t0.pubuser)        AS h_nick',
				't0.pubtime AS h_rposttime',
				't1.city',
				't2.grade   AS h_ograde',
				'(SELECT grade FROM hh_techuser WHERE id=t0.pubuser)       AS h_grade',
				't1.headcount',

				'(SELECT type FROM hh_techuser WHERE id=t0.pubuser)      AS h_official',
				'(SELECT rank FROM hh_techuser WHERE id=t0.pubuser)      AS h_rank',
				'(SELECT identified FROM hh_techuser WHERE id=t0.pubuser) AS h_identified',
				'(SELECT title FROM hh_rank WHERE dengji=(SELECT rankname FROM hh_techuser WHERE id=t0.pubuser)) AS h_rankname',

			),
			'filter' => array(
				't0.id'      => array('LT', Assign($params['tid'], 0)),
				't0.tid'     => 't1.id',
				't1.ofuser'  => 't2.id',
				't0.at'      => Assign($params['uid'], 0),
			),
			'others' => 'ORDER BY t0.id DESC',
			'column' => array(
				'ouid'      => 'h_ouid',
				'ouserpic'  => 'headerimg',
				'ousernick' => 'nick',
				'ograde'    => 'h_ograde',
				'rgrade'    => 'h_grade',
				'posttime'  => 'createdat',
				'job'       => 'job',
				'salary'    => 'salary',
				'city'      => 'city',
				'tid'       => 'h_tid',
				'messages'  => 'replycount',
				'newreply'  => 'isnewat',
				'ruid'      => 'h_ruid',
				'ruserpic'  => 'h_headerimg',
				'rusernick' => 'h_nick',
				'rposttime' => 'h_rposttime',
				'rcontext'  => 'h_rcontext',
				'mlistid'   => 'atlist',
				'headcount' => 'headcount',
				'mcontent'  => 'h_content',

				## 兼容字段
				'official'   => 'h_official',
				'identified' => 'h_identified',
				'rank'       => 'h_rank',
				'rankname'   => 'h_rankname',
				'reward'     => 'rewarded',
				'rewardata'  => 'reward',

			),
		);
		if (empty($params['tid'])) {
			$condition['filter']['t0.id'] = array('GT', Assign($params['databasever'], 0));
			$condition['others'] = 'ORDER BY t0.id DESC LIMIT 10';
		}
		switch ($params['type']) {
		case '2':
			$condition['filter']['t1.pubuser'] = Assign($params['uid'], 0);
			break;

		case '3':
			$sql = "(SELECT tid FROM hh_techuser_shoucang WHERE uid='%d' AND tag='%d' AND type='1')";
			$condition['filter']['t0.id'] = array('IN', sprintf($sql, $params['uid'], $params['tag']));
			break;
		}

		$edit = array(
			'schema' => 'hh_zhaopin_list',
			'fields' => array(
				'isnewat' => 0,
				'isnew'   => 0,
			),
			'filter' => array(
				'at' => Assign($params['uid'], 0),
			),
		);
	}

	$recordset = StorageFind($condition);
	if (is_array($recordset) == FALSE or empty($recordset) == TRUE) {
		$result['msg'] = MESSAGE_EMPTY;
	} else {
		$result = array('code' => '101', 'data' => array());

		foreach ($recordset as $index => $row) {
			$buffer = array(
				'collect'  => '0',
				'mypraise' => '0',
				'praises'  => '0',
			);

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

			$filter_count['touid'] = 1;
			if (StorageCount('hh_techuser_dianzan', $filter_count)) {
				$buffer['mypraise'] = '1';
			}

			$filter_total = array(
				'tid'  => Assign($params['tid'], 0),
				'tag'  => Assign($params['tag'], 0),
				'type' => '1',
				'touid' => 1,
			);
			$buffer['praises'] = Assign(StorageCount('hh_techuser_dianzan', $filter_total), 0);

			## 獲取圖片信息
			if (in_array($params['tag'], array('1', '2', '3', '4'))) {
				$buffer['omdata']  = array();
				$buffer['rmdata']  = array();
				$buffer['omedias'] = 0;
				$buffer['rmedias'] = 0;

				$condition_sub_o = array(
					'schema' => 'hh_techforum_img',
					'fields' => array('id'),
					'filter' => array(
						'qid' => Assign($row['h_tid'], 0),
					),
				);
				
				$buf_o = StorageFind($condition_sub_o);
				if (is_array($buf_o) and empty($buf_o) == FALSE) {
					foreach ($buf_o as $number => $row_img) {
						$buffer['omdata'][] = array(
							'mid'   => $row_img['id'],
							'type'  => '0',
							'mname' => 'image' . ($number + 1),
							'mpic'  => "{$row_img['id']}_s.png",
							'url'   => '',
						);

						$buffer['omedias'] += 1;
					}
				}

				$condition_sub_r = array(
					'schema' => 'hh_techforum_list_img',
					'fields' => array('id'),
					'filter' => array(
						'listid' => Assign($row['h_ouid'], 0),
					),
				);
				$buf_r = StorageFind($condition_sub_r);
				if (is_array($buf_r) and empty($buf_r) == FALSE) {
					foreach ($buf as $number => $row_img) {
						$buffer['omdata'][] = array(
							'mid'   => $row_img['h_ouid'],
							'type'  => '0',
							'mname' => 'image' . ($number + 1),
							'mpic'  => "{$row_img['id']}_s.png",
							'url'   => '',
						);

						$$buffer['rmedias'] += 1;
					}
				}

			}

			$result['data'][] = $buffer;
		}

		//StorageEdit($edit['schema'], $edit['fields'], $edit['filter']);
	}
}

