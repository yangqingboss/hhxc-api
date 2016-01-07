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
				'(SELECT headerimg FROM hh_techuser WHERE id=pubuser) AS h_headerimg',
				'(SELECT grade FROM hh_techuser WHERE id=pubuser) AS h_grade',
				'(SELECT COUNT(*) FROM hh_techqzhi_list_img WHERE listid=hh_techqzhi_list.id) AS h_ct',

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

					## 兼容字段
					'official'   => Assign($row['h_official'], 0),
					'identified' => Assign($row['h_identified'], 0),
					'rank'       => Assign($row['h_rank'], 0),
					'rankname'   => Assign($row['h_rankname']),

					'collect'   => '0', // 收藏状态
					'mypraise'  => '0', // 我的点赞状态
					'praises'   => '0', // 贴的点赞数量

				);

				$filter_count = array(
					'uid'  => Assign($params['uid'], 0),
					'tag'  => 3,
					'tid'  => $msg['listid'],
					'type' => 1,
				);
				if (StorageCount('hh_techuser_shoucang', $filter_count)) {
					$msg['collect'] = '1';
				}

				if ($params['type'] == '3' and $msg['collect'] == '0') {
					continue;
				}

				//$filter_count['touid'] = 0;
				if (StorageCount('hh_techuser_dianzan', $filter_count)) {
					$msg['mypraise'] = '1';
				}

				$filter_total = array(
					'tag'  => 3,
					'tid'  => $msg['listid'],
					'type' => 1,
				);
				$msg['praises'] = StorageCount('hh_techuser_dianzan', $filter_total);


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

		$schema = 'hh_techqzhi';
		$filter = array('tid' => Assign($params['tid'], 0), 'at' => Assign($params['uid'], 0));
		$buffer_host = StorageFindID($schema, Assign($params['tid'], 0));
		if (is_array($buffer_host) and empty($buffer_host) == FALSE) {
			if ($params['uid'] == $buffer_host['pubuser']) {
				StorageEdit($schema . '_list', array('isnew' => 0, 'isnewat' => 0), $filter);
				StorageEditByID($schema, array('isnewmsg' => 0, 'isnewat' => 0), $params['tid']);

			}

			## 取消點贊狀態
			RefreshMsgByCDZ($params['uid'], 3, 0);
			RefreshMsgByCDZ($params['uid'], 3, 1);

			RefreshMsg(Assign($params['uid'], 0));
			RefreshMsg(Assign($buffer_host['pubuser'], 0));

		}
	}
}

