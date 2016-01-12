<?php
// Copyright 2015 The Haohaoxiuche Team Authors. All right reserved.
// Use of this source that is governed by a Apache-style
// license that can be found in the LICENSE file.
//
// 技術版API編號119 被点赞信息
//
// @authors hjboss <hongjiangproject@yahoo.com> 2015-12-18#
// @version 1.0.0
// @package hhxc
if (!defined('HHXC')) die('Permission denied');

if (CheckOpenID($params['openid'], $params['uid']) == FALSE) {
	$result['msg'] = MESSAGE_WARNING;
} else {
	if ($params['tid'] > 0) {
		$result = array('code' => '101', 'data' => array());
		die(JsonEncode($result));
	}

	$tag     = 4;
	$keys    = array(array('ofuser', 'createdat'), array('pubuser', 'pubtime'));
	$schemas = array('hh_zhaopin', 'hh_zhaopin_list');
	$hostimg = FALSE;

	$result  = array('code' => '101', 'data' => array());
	foreach ($schemas as $index => $schema) {
		## 獲取當前用戶被點贊帖子
		$sql_in = "SELECT id FROM {$schema} WHERE {$keys[$index][0]}=%d";
		if ($tag == 1 or $tag == 2) $sql_in .= " AND type={$tag}";
		if ($index == '1') {
			$sql_in = "SELECT id FROM {$schema} WHERE {$keys[1][0]}=%d";
		}

		$condition_main = array(
			'schema' => 'hh_techuser_dianzan',
			'fields' => array(
				'DISTINCT tid',
			),
			'filter' => array(
				'tid'   => array('IN', sprintf($sql_in, Assign($params['uid'], 0))),
				'tag'   => $tag,
				'type'  => 1,
				'touid' => $index,
				'uid'   => array('GT', 0),
			),
			'others' => 'ORDER BY tid DESC LIMIT ' . (20 * PRAISE_NUMBER),
		);
		$recordset_main = StorageFind($condition_main);
		if (is_array($recordset_main) == FALSE or empty($recordset_main) == TRUE) {
			continue;
		}

		foreach ($recordset_main as $number_main => $row_main) {
			$buffer_info = StorageFindID($schemas[0], $row_main['tid']);
			if ($buffer_info['zhuangtai'] == 0) continue;

			$buffer_main = array(
				'tid'        => $row_main['tid'],
				'pubtime'    => 0,
				'createdat'  => 0,
				'praisetype' => $index == 0 ? '1' : '2',
				'newpraise'  => 0,
				'praisedata' => array(),
				'hostdata'   => array(),
				'repydata'   => array(),
			);

			## 帖子新點贊狀態
			$record_status = StorageFindID($schema, $row_main['tid']);
			if (is_array($record_status) and empty($record_status) == FALSE) {
				$buffer_main['newpraise'] = Assign($record_status['isnewdz'], 0);
				$buffer_main['pubtime']   = $record_status[$keys[$index][1]];
			}

			## 獲取點贊者信息 {{{
			$condition_main['fields'] = array('DISTINCT uid');
			$condition_main['filter']['tid'] = $row_main['tid'];
			$condition_main['others'] = 'ORDER BY id DESC LIMIT ' . PRAISE_NUMBER;
			$recordset_user = StorageFind($condition_main);
			if (is_array($recordset_user) and empty($recordset_user) == FALSE) {
				$sql_grade = '(SELECT title FROM hh_%s WHERE dengji=%s) AS h_%s';
				foreach ($recordset_user as $number_user => $row_user) {
					$condition_user = array(
						'schema' => 'hh_techuser',
						'fields' => array(
							'*',
							'grade AS h_grade',
							sprintf($sql_grade, 'rank',  'rankname', 'rankname'),
						),
						'filter' => array(
							'id' => $row_user['uid'],
						),
					);

					$record_user = StorageFindOne($condition_user);
					if (is_array($record_user) and empty($record_user) == TRUE) {
						continue;
					}

					## 獲取點贊時間
					$buffer_user_posttime = 0;
					$condition_user_posttime = array(
						'schema' => 'hh_techuser_dianzan',
						'filter' => array(
							'tag'   => $tag,
							'type'  => 1,
							'touid' => $index,
							'tid'   => $row_main['tid'],
							'uid'   => Assign($record_user['id'], 0),
						),
					);
					$record_user_posttime = StorageFindOne($condition_user_posttime);
					if (is_array($record_user_posttime) and empty($record_user_posttime) == FALSE){
						$buffer_user_posttime = $record_user_posttime['createdat'];
					}

					## 更新帖子被點贊最新時間
					if ($buffer_main['createdat'] < $buffer_user_posttime) {
						$buffer_main['createdat'] = $buffer_user_posttime;
					}

					## 構建點贊人信息
					$buffer_main['praisedata'][] = array(
						'uid'        => Assign($record_user['id'],         0),
						'userpic'    => Assign($record_user['headerimg']),
						'usernick'   => Assign($record_user['nick']),
						'grade'      => Assign($record_user['h_grade'],    0),
						'adopt'      => '0',
						'anonymous'  => '0',
						'official'   => Assign($record_user['type'],       0),
						'identified' => Assign($record_user['identified'], 0),
						'rank'       => Assign($record_user['rank'],       0),
						'rankname'   => Assign($record_user['h_rankname'], 0),
						'posttime'   => Assign($buffer_user_posttime),
						'content'    => '',
						'listid'     => '0',
						'index'      => '0',
						'medias'     => '0',
						'mdata'      => array(),
					);
				}
			} 
			## }}} 獲取點贊者信息

			## 獲取樓主信息 {{{
			$buffer_tid = $row_main['tid'];
			if ($index == 1) {
				$record_tid = StorageFindID($schema, $row_main['tid']);
				if (is_array($record_tid) and empty($record_tid) == FALSE) {
					$buffer_tid = $record_tid['tid'];
				}
			}

			$condition_host = array(
				'schema' => array('hh_techuser', $schemas[0]),
				'fields' => array(
					'*',
					't0.id AS h_uid',
					't1.id AS h_tid',
					't0.type AS h_official',
					"(SELECT COUNT(*) FROM {$schemas[1]} WHERE tid=t1.id) AS h_messages",
					't0.grade AS h_grade',
					##'t1.title AS h_title',
					##'t1.content AS h_content',
					'(SELECT title FROM hh_rank WHERE dengji=t0.rankname) AS h_rankname',
				),
				'filter' => array(
					't0.id' => "t1.{$keys[0][0]}",
					't1.id' => $buffer_tid,
				),
			);
			$record_host = StorageFindOne($condition_host);
			if (is_array($record_host) and empty($record_host) == FALSE) {
				$buffer_host = array(
					'uid'        => Assign($record_host['h_uid'],      0),
					'userpic'    => Assign($record_host['headerimg']),
					'usernick'   => Assign($record_host['nick']),
					'grade'      => Assign($record_host['h_grade'],    0),
					'anonymous'  => Assign($record_host['anonymous'],  0),
					'reward'     => Assign($record_host['reward'],     0),
					'posttime'   => Assign($record_host[$keys[0][1]]),
					'official'   => Assign($record_host['h_official'], 0),
					'identified' => Assign($record_host['identified'], 0),
					'rank'       => Assign($record_host['rank'],       0),
					'rankname'   => Assign($record_host['h_rankname']),
					'title'      => Assign($record_host['h_title']),
					'context'    => Assign($record_host['h_content']),
					'collect'    => 0,
					'mypraise'   => 0,
					'praises'    => 0,
					'tid'        => Assign($record_host['h_tid'],      0),
					'messages'   => Assign($record_host['h_messages'], 0),
					'medias'     => 0,
					'mdata'      => array(),

					## 兼容字段
					'level'      => Assign($record_host['level'],      0),
					'experience' => Assign($record_host['experience'], 0),
					'city'       => Assign($record_host['city']),
					'job'        => Assign($record_host['job']),
					'salary'     => Assign($record_host['salary']),
					'headcount'  => Assign($record_host['headcount'],  0),
				);

				## 查詢收藏狀態
				$filter_host_count = array(
					'uid'   => $buffer_host['uid'],
					'tid'   => $buffer_host['tid'],
					'tag'   => $tag,
					'type'  => 1,
				);
				if (StorageCount('hh_techuser_shoucang', $filter_host_count)) {
					$buffer_host['collect'] = '1';
				}

				## 查詢被點贊狀態和總數
				$filter_host_count['touid'] = $index;
				if (StorageCount('hh_techuser_dianzan', $filter_host_count)) {
					$buffer_host['mypraise'] = '1';
				}

				$filter_host_total = array(
					'tid'   => $buffer_host['tid'],
					'tag'   => $tag,
					'type'  => 1,
					'touid' => $index,
				);
				$buffer_host['praises'] = StorageCount('hh_techuser_dianzan', $filter_host_total);

				## 獲取帖子圖片
				if ($hostimg == TRUE) {
					$condition_host_img = array(
						'schema' => $schemas[0] . '_img',
						'filter' => array(
							'qid' => $buffer_host['tid'],
						),
					);

					$buffer_host_img = StorageFind($condition_host_img);
					if (is_array($buffer_host_img) and empty($buffer_host_img) == FALSE) {
						foreach ($buffer_host_img as $number_img => $row_img) {
							$buffer_host['mdata'][] = array(
								'mid'   => $row_img['id'],
								'type'  => '0',
								'mname' => 'image' . ($number_repy + 1),
								'mpic'  => "{$row_img['id']}_s.png",
								'url'   => '',
							);
						}
					}
				}

				$buffer_main['hostdata'][] = $buffer_host;
			} else {
				continue;
			}
			## }}} 獲取樓主信息

			## 獲取回帖者信息 {{{
			if ($buffer_main['praisetype'] == '2') {
				$condition_repy = array(
					'schema' => array('hh_techuser', $schemas[1]),
					'fields' => array(
						'*',
						't0.id AS h_uid',
						't1.id AS h_tid',
						't0.type AS h_official',
						't1.content AS h_content',
						't0.grade AS h_grade',
						'(SELECT title FROM hh_rank WHERE dengji=t0.rankname) AS h_rankname',
					),
					'filter' => array(
						't0.id'  => "t1.{$keys[1][0]}",
						't1.tid' => $buffer_tid,
					),
					'others' => "ORDER BY t1.{$keys[1][1]} DESC",
				);

				$recordset_repy = StorageFind($condition_repy);
				if (is_array($recordset_repy) and empty($recordset_repy) == FALSE) {
					foreach ($recordset_repy as $number_repy => $row_repy) {
						$buffer_repy = array(
							'uid'        => Assign($row_repy['h_uid'],       0),
							'userpic'    => Assign($row_repy['headerimg']),
							'usernick'   => Assign($row_repy['nick']),
							'grade'      => Assign($row_repy['h_grade']),
							'adopt'      => Assign($row_repy['adopt']),
							'anonymous'  => Assign($row_repy['anonymous']),
							'official'   => Assign($row_repy['h_official'], 0),
							'identified' => Assign($row_repy['identified'], 0),
							'rank'       => Assign($row_repy['rank'],       0),
							'rankname'   => Assign($row_repy['h_rankname'], 0),
							'posttime'   => Assign($row_repy[$keys[1][1]]),
							'context'    => Assign($row_repy['h_content']),
							'listid'     => $buffer_tid,
							'index'      => Assign($row_repy['no'],         0),
							'medias'     => '0',
							'mdata'      => array(),

							## 兼容字段
							'content'    => Assign($row_repy['h_content']),
						);

						## 獲取圖片信息
						$condition_repy_img = array(
							'schema' => $schemas[1] . '_img',
							'filter' => array(
								'listid' => $row_repy['h_tid'],
							),
						);

						$buffer_repy_img = StorageFind($condition_repy_img);
						if (is_array($buffer_repy_img) and empty($buffer_repy_img) == FALSE) {
							foreach ($buffer_repy_img as $number_img => $row_img) {
								$buffer_repy['mdata'][] = array(
									'mid'   => $row_img['id'],
									'type'  => '0',
									'mname' => 'image' . ($number_repy + 1),
									'mpic'  => "{$row_img['id']}_s.png",
									'url'   => '',
								);
							}
						}
						$buffer_repy['medias'] = count($buffer_repy['mdata']);

						$buffer_main['repydata'][] = $buffer_repy;
					}
				}
			}

			## }}} 獲取回帖者信息
		
			$result['data'][] = $buffer_main;
		}
	}

	## 數據列表排序
	if (empty($result['data']) == TRUE) {
		$result['msg'] = MESSAGE_EMPTY;
	} else {
		for ($number0 = 0; $number0 < count($result['data']) - 1; $number0++) {
			for ($number1 = $number0 + 1; $number1 < count($result['data']); $number1++) {
				if ($result['data'][$number0]['pubtime'] < $result['data'][$number1]['pubtime']) {
					$buffer_tmp = $result['data'][$number0];
					$result['data'][$number0] = $result['data'][$number1];
					$result['data'][$number1] = $buffer_tmp;
				}
			}
		}
	}
}

