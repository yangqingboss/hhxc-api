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

			$result['data'][] = $buffer;
		}

		$filter = array('tid' => Assign($params['tid'], 0), 'at' => Assign($params['uid'], 0));
		StorageEdit('hh_techforum_list', array('isnew' => 0, 'isnewat' => 0), $filter);
		StorageEditByID('hh_techforum', array('isnewmsg' => 0, 'isnewat' => 0), Assign($params['tid'], 0));
		RefreshMsg(Assign($params['uid'], 0));
	}
}

