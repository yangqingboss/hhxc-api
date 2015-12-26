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
	$tag = 2;
	$pub = 'pubuser';
	$schemas = array('hh_techforum', 'hh_techforum_list');

	$result = array('code' => '101', 'data' => array());
	foreach ($schemas as $index => $schema) {
		$buffers = array();

		$condition_main = array(
			'schema' => 'hh_techuser_dianzan',
			'fields' => array(
				'*',
				"(SELECT isnewdz  FROM {$schema} WHERE id=hh_techuser_dianzan.tid) AS h_isnewdz",
				//"(SELECT lasttime FROM {$schema} WHERE id=hh_techuser_dianzan.tid) AS h_lasttime",
			),
			'filter' => array(
				'tag'   => $tag,
				'tid'   => array('IN', "SELECT tid FROM {$schema} WHERE {$pub}='{$params['uid']}'"),
				'type'  => 1,
				'touid' => $index,
			),
			'others' => 'ORDER BY tid DESC',
		);
		$recordset_main = StorageFind($condition_main);
		if (is_array($recordset_main) == FALSE or empty($recordset_main) == TRUE) {
			continue;
		}
	
		foreach ($recordset_main as $number => $row_main) {
			if (empty($buffers[$row_main['tid']]) == FALSE) {
				continue;
			}

			$buffer = array(
				'praisetype' => empty($row_main['touid']) ? '1' : '2',
				'newpraise'  => $row_main['h_isnewdz'],
				'lasttime'   => $row_main['h_lasttime'],
				'praisedata' => array(),
			);

			## 獲取點贊人信息
			foreach ($recordset_main as $number0 => $row_user) {
				if ($row_user['tid'] != $row_main['tid']) {
					continue;
				}

				$recordset_user = array(
					'schema' => 'hh_techuser',
					'fields' => array(
						'*',
					),
					'filter' => array(
						'id' => $row_user['uid'],
					),
				);
				$record_user = StorageFindOne($recordset_user);
				if (is_array($record_user) and empty($record_user) == FALSE) {
					$buffer_user = array(
						'uid'        => $record_user['id'], 
						'userpic'    => $record_user['headerimg'],
						'usernick'   => $record_user['nick'],
						'grade'      => '',
						'adopt'      => $record_user['adopt'],
						'anonymous'  => $record_user['anonymous'],
						'official'   => '',
						'identified' => '',
						'rank'       => 0,
						'rankname'   => '',
						'posttime'   => '',
					);

					$buffer['praisedata'][] = $buffer_user;
				}
			}

			## 獲取樓主信息

			## 獲取跟貼信息

			$buffers[$row_main['tid']] = $buffer;

		}

		## 添加
	}

	if (count($result['data']) == 0) {
		$result['msg'] = MESSAGE_EMPTY;
	} else {
		foreach ($buffers as $buffer) {
			$result['data'][] = $buffer;
		}
	}
}

