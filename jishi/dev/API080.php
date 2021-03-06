<?php
// Copyright 2015 The Haohaoxiuche Team Authors. All right reserved.
// Use of this source that is governed by a Apache-style
// license that can be found in the LICENSE file.
//
// 技術版API編號080 汽修人之主題詳細 ##代替072
//
// @authors hjboss <hongjiangproject@yahoo.com> 2015-12-18#
// @version 1.0.0
// @package hhxc
if (!defined('HHXC')) die('Permission denied');

if (CheckOpenID($params['openid'], $params['uid']) == FALSE) {
	$result['msg'] = MESSAGE_WARNING;
} else {
	$condition = array(
		'schema' => 'hh_techforum_list',
		'fields' => array(
			'*',
			'(SELECT nick FROM hh_techuser WHERE id=pubuser) AS h_nick',
			'(SELECT headerimg FROM hh_techuser WHERE id=pubuser) AS h_headerimg',
			'(SELECT grade FROM hh_techuser WHERE id=pubuser) AS h_grade',
			'(SELECT COUNT(*) FROM hh_techforum_list_img WHERE listid=hh_techforum_list.id) AS h_medias',
			'(SELECT type FROM hh_techuser WHERE id=pubuser)      AS h_official',
			'(SELECT rank FROM hh_techuser WHERE id=pubuser)      AS h_rank',
			'(SELECT identified FROM hh_techuser WHERE id=pubuser) AS h_identified',
			'(SELECT title FROM hh_rank WHERE dengji=(SELECT rankname FROM hh_techuser WHERE id=pubuser)) AS h_rankname',
		),
		'filter' => array(
			'tid' => Assign($params['tid'], 0),
			'no'  => array('GT', Assign($params['index'], 0)),
		),
	);

	
	$recordset = StorageFind($condition);
	if (is_array($recordset) == FALSE or empty($recordset) == TRUE) {
		$result['msg'] = MESSAGE_EMPTY;
	} else {
		$result = array('code' => '101', 'data' => array());

		foreach ($recordset as $index => $row) {
			$buffer = array(
				'uid'      => $row['pubuser'],
				'tid'      => $row['tid'],
				'userpic'  => $row['h_headerimg'],
				'usernick' => $row['h_nick'],
				'grade'    => $row['h_grade'],
				'posttime' => $row['pubtime'],
				'content'  => $row['content'],
				'listid'   => $row['id'],
				'index'    => $row['no'],
				'medias'   => $row['h_medias'],
				'mdata'    => array(),

				## 兼容字段
				'official'   => Assign($row['h_official'], 0),
				'identified' => Assign($row['h_identified'], 0),
				'rank'       => Assign($row['h_rank'], 0),
				'rankname'   => Assign($row['h_rankname']),

				'adopt'      => Assign($row['adopt'], 0),
				'mypraise'   => '0',
				'praises'    => '0',
			);

			$condition_sub = array(
				'schema' => 'hh_techforum_list_img',
				'fields' => array('id'),
				'filter' => array(
					'listid' => $row['id'],
				),
			);
			$buf = StorageFind($condition_sub);
			if (is_array($buf) and empty($buf) == FALSE) {
				foreach ($buf as $index => $row_img) {
					$buffer['mdata'][] = array(
						'mid'   => $row_img['id'],
						'type'  => '0',
						'mname' => 'image' . ($index+1),
						'mpic'  => $row_img['id'] . '_s.png',
						'url'   => '',
					);
				}
			}

			$filter = array(
				//'uid'   => Assign($params['uid'], 0),
				'tag'   => $params['tag'],
				'tid'   => $buffer['listid'],
				'type'  => 1,
				'touid' => 1,
			);
			if (empty($params['uid']) == FALSE) $filter['uid'] = Assign($params['uid'], 0);
			if (StorageCount('hh_techuser_dianzan', $filter)) {
				$buffer['mypraise'] = '1';
			}

			$filter = array(
				//'uid'   => Assign($params['uid'], 0),
				'tag'   => $params['tag'],
				'tid'   => $buffer['listid'],
				'type'  => 1,
				'touid' => 1,
			);
			//if (empty($params['uid']) == FALSE) $filter['uid'] = Assign($params['uid'], 0);
			$buffer['praises'] = Assign(StorageCount('hh_techuser_dianzan', $filter), 0);

			$result['data'][] = $buffer;
		}

		$schema = 'hh_techforum';
		$filter0 = array(
			'tid'     => Assign($params['tid'], 0), 
			'pubuser' => Assign($params['uid'], 0),
			'type'    => Assign($params['tag'], 0),
		);
		$filter1 = array( 
			'type' => Assign($params['tag'], 0),
			'tid'  => Assign($params['tid'], 0),
			'at'   => Assign($params['uid'], 0),
		);
		$buffer_host = StorageFindID('hh_techforum', Assign($params['tid'], 0));
		if (is_array($buffer_host) and empty($buffer_host) == FALSE) {
			if ($params['uid'] == $buffer_host['pubuser']) {
				StorageEditByID($schema, array('isnewmsg' => 0, 'isnewat' => 0), $params['tid']);
			}
		}
		StorageEdit($schema . '_list', array('isnew' => 0, 'isnewat' => 0), $filter0);
		StorageEdit($schema . '_list', array('isnew' => 0, 'isnewat' => 0), $filter1);

		## 取消點贊狀態
		RefreshMsgByCDZ($buffer_host['pubuser'], $params['tag'], 0);
		RefreshMsgByCDZ($buffer_host['pubuser'], $params['tag'], 1);
		RefreshMsg(Assign($buffer_host['pubuser'], 0));

	}

	## 消除點贊狀態
	StorageEditByID('hh_techforum', array('isnewdz' => 0), Assign($params['tid'], 0));
	RefreshMsgByCDZ($params['uid'], $params['tag'], 0);
	RefreshMsgByCDZ($params['uid'], $params['tag'], 1);
	RefreshMsg(Assign($params['uid'], 0));
}

