<?php
// Copyright 2015 The Haohaoxiuche Team Authors. All right reserved.
// Use of this source that is governed by a Apache-style
// license that can be found in the LICENSE file.
//
// 技術版API編號120 生活點贊
//
// @authors hjboss <hongjiangproject@yahoo.com> 2015-12-18#
// @version 1.0.0
// @package hhxc
if (!defined('HHXC')) die('Permission denied');

if (CheckOpenID($params['openid'], $params['uid']) == FALSE) {
	$result['msg'] = MESSAGE_WARNING;
} else {
	$tag     = 2;
	$keys    = array(
		array('pubuser', 'pubtime'), 
		array('pubuser', 'pubtime'),
	);
	$schemas = array('hh_techforum', 'hh_techforum_list');

	$result = array('code' => '101', 'data' => array());
	foreach ($schemas as $index => $schema) {
		## 獲取當前用戶被點贊的帖子
		$sql_in = "SELECT id FROM {$schema} WHERE {$keys[$index][0]}='%d'";
		$condition_main = array(
			'schema' => 'hh_techuser_dianzan',
			'fields' => array(
				'DISTINCT tid',
			),
			'filter' => array(
				'tid'   => array('IN', sprintf($sql_in, $params['uid'])),
				'tag'   => $tag,
				'type'  => 1,
				'touid' => $index,
				'uid'   => array('GT', 0),
			),
			'others' => 'ORDER BY tid DESC',
		);
		$recordset_main = StorageFind($condition_main);
		if (is_array($recordset_main) == FALSE or empty($recordset_main) == TRUE) {
			continue;
		}

		foreach ($recordset_main as $number_main => $row_main) {
			$buffer_main = array(
				'tid'        => $row_main['tid'],
				'pubtime'    => '0',
				'praisetype' => empty($row_main['touid']) ? '1' : '2',
				'newpraise'  => '0',
				'praisedata' => array(),
				'hostdata'   => array(),
				'repydata'   => array(),
			);

			## 新點贊狀態
			$record_status = StorageFindID($schema, $row_main['tid']);
			if (is_array($record_status) and empty($record_status) == FALSE) {
				$buffer_main['newpraise'] = Assign($record_status['isnewdz'], '0');
				$buffer_main['pubtime']   = $record_status[$keys[$index][1]];
			}

			## 獲取點贊者信息
			$condition_main['fields'] = array('DISTINCT uid');
			$condition_main['filter']['tid'] = $row_main['tid'];
			$recordset_user = StorageFind($condition_main);
			if (is_array($recordset_user) and empty($recordset_user) == FALSE) {
				foreach ($recordset_user as $number_user => $row_user) {
					$condition_user = array(
						'schema' => 'hh_techuser',
						'fields' => array(
							'*',
							'(SELECT title FROM hh_score WHERE dengji=grade) AS h_grade',
						),
						'filter' => array(
							'id' => $row_user['uid'],
						),
					);

					$record_user = StorageFindOne($condition_user);
					if (is_array($record_user) == FALSE or empty($record_user) == TRUE) {
						continue;
					}

					$buffer_main['praisedata'][] = array(
						'uid'        => $record_user['id'],
						'userpic'    => $record_user['headerimg'],
						'usernick'   => $record_user['nick'],
						'grade'      => $record_user['h_grade'],
						'adopt'      => '0',
						'anonymous'  => '0',
						'official'   => $record_user['type'],
						'identified' => $record_user['identified'],
						'rank'       => $record_user['rank'],
						'rankname'   => $record_user['rankname'],
						'posttime'   => '0',
						'content'    => '0',
						'listid'     => '0',
						'index'      => '0',
						'medias'     => '0',
						'mdata'      => array(),
					);
				}
			}

			## 樓主帖子ID
			$buffer_tid = $row_main['tid'];
			if ($buffer_main['praisetype'] == '2') {
				$record_tid = StorageFindID($schema, $row_main['tid']);
				if (is_array($record_tid) and empty($record_tid) == FALSE) {
					$buffer_tid = $record_tid['tid'];
				}
			}

			## 獲取樓主信息
			$condition_host = array(
				'schema' => array('hh_techuser', $schemas[0]),
				'fields' => array(
					'*',
					't0.id AS h_uid',
					't1.id AS h_tid',
					't0.type AS h_official',
					"(SELECT COUNT(*) FROM {$schemas[1]} WHERE tid=t1.id) AS h_messages",
					'(SELECT title FROM hh_score WHERE dengji=t0.grade) AS h_grade',
					't1.title AS h_title',
					't1.content AS h_content',
				),
				'filter' => array(
					't0.id' => "t1.{$keys[1][0]}",
					't1.id' => $buffer_tid,
				),
			);
			$record_host = StorageFindOne($condition_host);
			if (is_array($record_host) and empty($record_host) == FALSE) {
				$buffer_host = array(
					'uid'        => Assign($record_host['h_uid'], 0),
					'userpic'    => Assign($record_host['headerimg']),
					'usernick'   => Assign($record_host['nick']),
					'grade'      => Assign($record_host['h_grade'], 0),
					'official'   => Assign($record_host['h_official'], 0),
					'identified' => Assign($record_host['identified'], 0),
					'rank'       => Assign($record_host['rank'], 0),
					'rankname'   => Assign($record_host['h_rankname']),
					'posttime'   => Assign($record_host[$keys[0][1]]),
					'job'        => Assign($record_host['job']),
					'salary'     => Assign($record_host['salary']),
					'headcount'  => Assign($record_host['headcount']),
					'city'       => Assign($record_host['city']),
					'messages'   => Assign($record_host['h_messages'], 0),
					'title'      => Assign($record_host['h_title']),
					'content'    => Assign($record_host['h_content']),
					'collect'    => '0',
					'mypraise'   => '0',
					'praises'    => '0',
				);

				## 收藏狀態
				$filter_host_count = array(
					'uid'   => $buffer_host['uid'],
					'tid'   => $buffer_host['tid'],
					'tag'   => $tag,
					'type'  => 1,
				);
				if (StorageCount('hh_techuser_shoucang', $filter_host_count)) {
					$buffer_host['collect'] = '1';
				}

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
				$buffer['praises'] = StorageCount('hh_techuser_dianzan', $filter_host_total);

				$buffer_main['hostdata'][] = $buffer_host;
			}

			## 回帖信息
			if ($buffer_main['praisetype'] == '2') {
				$condition_repy = array(
					'schema' => array('hh_techuser', $schemas[1]),
					'fields' => array(
						'*',
						't0.id AS h_uid',
						't1.id AS h_tid',
						't0.type AS h_official',
						'(SELECT title FROM hh_score WHERE dengji=t0.grade) AS h_grade',
						't1.title AS h_title',
						't1.content AS h_content',
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
							'uid'        => Assign($row_repy['h_uid'], 0),
							'userpic'    => Assign($row_repy['headerimg']),
							'usernick'   => Assign($row_repy['nick']),
							'grade'      => Assign($row_repy['h_grade']),
							'adopt'      => Assign($row_repy['adopt']),
							'anonymous'  => Assign($row_repy['anonymous']),
							'official'   => Assign($row_repy['h_official'], 0),
							'identified' => Assign($row_repy['identified'], 0),
							'rank'       => Assign($row_repy['rank'], 0),
							'rankname'   => Assign($row_repy['rankname'], 0),
							'posttime'   => Assign($row_repy[$keys[1][1]]),
							'content'    => Assign($row_repy['h_content']),
							'listid'     => $buffer_tid,
							'title'      => Assign($record_host['h_title']),
							'index'      => Assign($row_repy['no'], 0),
							'medias'     => '0',
							'mdata'      => array(),
						);

						## 獲取圖片信息
						$condition_repy_img = array(
							'schema' => $schemas[1] . '_img',
							'filter' => array(
								'id' => $row_repy['h_tid'],
							),
						);
						$buffer_repy_img = StorageFind($condition_repy_img);
						if (is_array($buffer_repy_img) and empty($buffer_repy_img) == FALSE) {
							foreach ($record_repy_img as $number_img => $row_img) {
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

			$result['data'][] = $buffer_main;
		}
	}

	## 帖子信息排序
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

