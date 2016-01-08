<?php
// Copyright 2015 The Haohaoxiuche Team Authors. All right reserved.
// Use of this source that is governed by a Apache-style
// license that can be found in the LICENSE file.
//
// 技術版API編號073 汽修人更貼 ##代替014 
//
// @authors hjboss <hongjiangproject@yahoo.com> 2015-12-18#
// @version 1.0.0
// @package hhxc
if (!defined('HHXC')) die('Permission denied');

if (CheckOpenID($params['openid'], $params['uid']) == FALSE) {
	$result['msg'] = MESSAGE_WARNING;
} else {
	$is_newat = ($params['touid'] > 0 and $params['touid'] != $params['uid']) ? 1 : 0;

	## 添加更貼信息
	$schema = ''; $data = array();
	switch ($params['tag']) {
	case '3':
		$schema = 'hh_techqzhi';
		$data = array(
			'pubuser'   => Assign($params['uid'], 0),
			'type'      => Assign($params['tag'], 0),
			'tid'       => Assign($params['tid'], 0),
			'content'   => Assign($params['content']),
			'pubtime'   => 'NOW()',
			'anonymous' => Assign($params['anonymous']),
			'no'        => "(SELECT maxno FROM {$schema} WHERE id='{$params['tid']}')",
			'at'        => Assign($params['touid'], 0),
			'atlist'    => Assign($params['tolistid']),
			'isnewat'   => $is_newat,
		);
		break;

	case '4':
		$schema = 'hh_zhaopin';
		$data = array(
			'pubuser'   => Assign($params['uid'], 0),
			'type'      => Assign($params['tag'], 0),
			'tid'       => Assign($params['tid'], 0),
			'content'   => Assign($params['content']),
			'pubtime'   => 'NOW()',
			'anonymous' => Assign($params['anonymous']),
			'no'        => "(SELECT maxno FROM {$schema} WHERE id='{$params['tid']}')",
			'at'        => Assign($params['touid'], 0),
			'atlist'    => Assign($params['tolistid']),
			'isnewat'   => $is_newat,
		);
		break;

	default:
		$schema = 'hh_techforum';
		$data = array(
			'pubuser'   => Assign($params['uid'], 0),
			'type'      => Assign($params['tag'], 0),
			'tid'       => Assign($params['tid'], 0),
			'content'   => Assign($params['content']),
			'pubtime'   => 'NOW()',
			'anonymous' => Assign($params['anonymous']),
			'no'        => "(SELECT maxno FROM {$schema} WHERE id='{$params['tid']}')",
			'at'        => Assign($params['touid'], 0),
			'atlist'    => Assign($params['tolistid']),
			'isnewat'   => $is_newat,
		);
	}

	$id = StorageAdd("{$schema}_list", $data);
	if (empty($id) == TRUE) {
		$result['msg'] = '发送失败！';
	} else {
		$record = StorageFindID($schema, Assign($params['tid'], 0));
		
		## 更新更貼統計數據
		$fields = array(); $column = '';
		switch ($params['tag']) {
		case '3':
			$column = 'pubuser';
			$fields = array(
				'maxno'      => 'maxno+1',
				'replycount' => "(SELECT COUNT(*) FROM {$schema}_list WHERE tid='{$params['tid']}')",
				'isnewmsg'   => ($record[$column] == $params['uid']) ? 0 : 1,
				'isnewat'    => empty($params['touid']) ? 0 : 1,
			);
			break;

		case '4':
			$column = 'ofuser';
			$fields = array(
				'maxno'      => 'maxno+1',
				'replycount' => "(SELECT COUNT(*) FROM {$schema}_list WHERE tid='{$params['tid']}')",
				'isnewmsg'   => ($record[$column] == $params['uid']) ? 0 : 1,
				'isnewat'    => empty($params['touid']) ? 0 : 1,
			);
			break;

		default:
			$column = 'pubuser';
			$fields = array(
				'maxno'      => 'maxno+1',
				'replycount' => "(SELECT COUNT(*) FROM {$schema}_list WHERE tid='{$params['tid']}')",
				'isnewmsg'   => ($record[$column] == $params['uid']) ? 0 : 1,
				'isnewat'    => empty($params['touid']) ? 0 : 1,
			);
		}
		
		StorageEditByID($schema, $fields, Assign($params['tid'], 0));

		$result = array('code' => '101', 'data' => array());

		## 獲取更貼人信息
		$condition = array(
			'schema' => "{$schema}_list",
			'fields' => array(
				'pubtime', 
				'no',
				'id',
				"(SELECT {$column} FROM {$schema} WHERE id={$schema}_list.id) AS h_pubuser",
			),
			'filter' => array(
				'id' => $id,
			),
		);
		$buf = StorageFindOne($condition);
		if (is_array($buf) and empty($buf) == FALSE) {
			$result['data'][] = array(
				'posttime' => $buf['pubtime'],
				'index'    => $buf['no'],
				'listid'   => $buf['id'],
			);
		}

		## 更新相關用戶消息
		RefreshMsg(Assign($parmas['uid'],   0));
		RefreshMsg(Assign($params['touid'], 0));
		RefreshMsg(Assign($buf['h_pubuser'], 0));
		
	}
	
}

