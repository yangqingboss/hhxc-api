<?php
// Copyright 2015 The Haohaoxiuche Team Authors. All right reserved.
// Use of this source that is governed by a Apache-style
// license that can be found in the LICENSE file.
//
// 技術版API編號082 汽修人之求職主題詳細 ##代替074
//
// @authors hjboss <hongjiangproject@yahoo.com> 2015-12-18#
// @version 1.0.0
// @package hhxc
if (!defined('HHXC')) die('Permission denied');

if (CheckOpenID($params['openid'], $params['uid']) == FALSE) {
	$result['msg'] = MESSAGE_WARNING;
} else {
	$record = StorageFindID('hh_techqzhi', Assign($params['tid'], 0));
	if (is_array($record) == FALSE or empty($record) == TRUE) {
		$result['msg'] = MESSAGE_EMPTY;
	} else {
		$result = array('code' => '101', 'data' => array());

		$buffer = array(
			'cars'      => $record['cars'],
			'job'       => $record['job'],
			'introduce' => $record['introduce'],
			'messages'  => array(),
		);

		$condition = array(
			'schema' => 'hh_techqzhi_list',
			'fields' => array(
				'*',
				'(SELECT nick FROM hh_techuser WHERE id=pubuser) AS h_nick',
				'(SELECT headerimg FROM hh_techuser WHERE id=pubuser) AS h_headerimg)',
				'(SELECT grade FROM hh_techuser WHERE id=pubuser) AS h_grade)',
				'(SELECT COUNT(*) FROM hh_techqzhi_list_img WHERE listid=hh_techqzhi_list.id) AS h_ct',
			),
			'filter' => array(
				'tid' => Assign($params['tid'], 0),
				'no'  => array('GT', Assign($params['index'], 0)),
			),
		);
		$recordset = StorageFind($condition);
		if (is_array($recordset) and empty($record) == FALSE) {
			foreach ($recordset as $index => $row) {
				$msg = array(
					'uid'      => $row['pubuser'],
					'userpic'  => $row['h_headerimg'],
					'usernick' => $row['h_nick'],
					'grade'    => $row['h_grade'],
					'posttime' => $row['pubtime'],
					'content'  => $row['content'],
					'index'    => $row['no'],
					'listid'   => $row['id'],
					'medias'   => $row['h_ct'],
					'mdata'    => array(),
				);

				$condition_sub = array(
					'schema' => 'hh_techqzhi_list_img',
					'fields' => array('id'),
					'filter' => array(
						'listid' => $row['id'],
					),
				);
				$buf = StorageFind($condition_sub);
				if (is_array($buf) and empty($buf) == FALSE) {
					foreach ($buf as $number => $row_img) {
						$msg['mdata'][] = array(
							'mid'   => $row_img['id'],
							'type'  => 0,
							'mname' => 'image' . ($number + 1),
							'mpic'  => $row_img['id'] . '_s.png',
							'url'   => '',
						);
					}
				}

				$buffer['messages'][] = $msg;
			}
		}

		$result['data'][] = $buffer;

		## 更新狀態
		$filter = array(
			'tid' => Assign($params['tid'], 0),
			'at'  => Assign($params['uid'], 0),
		);
		StorageEdit('hh_techqzhi_list', array('isnew' => 0, 'isnewat' => 0), $filter);
		StorageEditByID('hh_techqzhi', array('isnewmsg' => 0, 'isnewat' => 0), Assign($params['tid'], 0));
		RefreshMsg(Assign($params['uid'], 0));
	}
}

