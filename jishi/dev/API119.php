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
	$tag     = 1;
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

			## 獲取樓主信息
			$result['data'][] = $buffer_main;
		}
	}

	## 帖子信息排序
	if (empty($result['data']) == TRUE) {
		$result['msg'] = MESSAGE_EMPTY;
	} else {
	}
}

