<?php
// Copyright 2015 The Haohaoxiuche Team Authors. All right reserved.
// Use of this source that is governed by a Apache-style
// license that can be found in the LICENSE file.
//
// 技術版API編號092 汽修人之招聘主題詳細 ##參考082
//
// @authors hjboss <hongjiangproject@yahoo.com> 2015-12-18#
// @version 1.0.0
// @package hhxc
if (!defined('HHXC')) die('Permission denied');

if (CheckOpenID($params['openid'], $params['uid']) == FALSE) {
	$result['msg'] = MESSAGE_WARNING;
} else {
	$result = array('code' => '101', 'data' => array());

	$record = StorageFindID('hh_zhaopin', $params['tid']);
	if (is_array($record) and empty($record) == FALSE) {
		$buffer = array(
			'contactinfo' => Assign($record['contactinfo']),
			'boon'        => Assign($record['boon']),
			'business'    => Assign($record['business']),
			'scale'       => Assign($record['scale']),
			'name'        => Assign($record['name']),
			'location'    => Assign($record['location']),
			'etc'         => Assign($record['etc']),
			'replycount'  => Assign($record['replycount'], 0),
			'messages'    => array(),
		);

		$condition = array(
			'schema' => 'hh_zhaopin_list',
			'fields' => array(
				'*',
				'(select nick from hh_techuser where id=pubuser) AS h_nick',
				'(select headerimg from hh_techuser where id=pubuser) AS h_headerimg',
				'(select grade from hh_techuser where id=pubuser) AS h_grade',
				'(select count(*) from hh_zhaopin_list_img where listid=hh_zhaopin_list.id) AS h_ct',
				
				'(SELECT type FROM hh_techuser WHERE id=pubuser)       AS h_official',
				'(SELECT rank FROM hh_techuser WHERE id=pubuser)       AS h_rank',
				'(SELECT identified FROM hh_techuser WHERE id=pubuser) AS h_identified',
				'(SELECT title FROM hh_rank WHERE dengji=(SELECT rankname FROM hh_techuser WHERE id=pubuser)) AS h_rankname',
			),
			'filter' => array(
				'tid' => Assign($params['tid'], 0),
				'no'  => array('GT', Assign($params['index'], 0)),
			),
		);

		$recordset = StorageFind($condition);
		if (is_array($recordset) and empty($recordset) == FALSE) {
			foreach ($recordset as $number => $row) {
				$buf = array(
					'uid'      => Assign($row['pubuser'], 0),
					'userpic'  => Assign($row['h_headerimg']),
					'usernick' => Assign($row['h_nick']),
					'grade'    => Assign($row['h_grade']),
					'posttime' => Assign($row['pubtime']),
					'content'  => Assign($row['content']),
					'index'    => Assign($row['no'], 0),
					'listid'   => Assign($row['id'], 0),
					'medias'   => Assign($row['h_ct'], 0),
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
					'tag'  => 4,
					'tid'  => $buf['listid'],
					'type' => 1,
				);
				if (StorageCount('hh_techuser_shoucang', $filter_count)) {
					$buf['collect'] = '1';
				}

				if ($params['type'] == '3' and $buf['collect'] == '0') {
					continue;
				}

				$filter_count['touid'] = 1;
				if (StorageCount('hh_techuser_dianzan', $filter_count)) {
					$buf['mypraise'] = '1';
				}

				$filter_total = array(
					'tag'  => 4,
					'tid'  => $buf['listid'],
					'type' => 1,
					'touid' => 1,
				);
				$buf['praises'] = Assign(StorageCount('hh_techuser_dianzan', $filter_total), 0);

				$condition_img = array(
					'schema' => 'hh_zhaopin_list_img',
					'filter' => array(
						'listid' => $row['id'],
					),
				);
				$recordset_img = StorageFind($condition_img);
				if (is_array($recordset_img) and empty($recordset_img) == FALSE) {
					foreach ($recordset_img as $number_img => $row_img) {
						$buf['mdata'][] = array(
							'mid'   => $row_img['id'],
							'type'  => 0,
							'mname' => 'image' . ($number_img + 1),
							'mpic'  => $row_img['id'] . '_s.png',
							'url'   => '',
						);
					}
				}

				$buffer['messages'][] = $buf;
			}
		}

		$result['data'][] = $buffer;

		$schema = 'hh_zhaopin';
		$filter0 = array(
			'tid' => Assign($params['tid'], 0), 
			'pubuser' => Assign($params['uid'], 0),
		);
		$filter1 = array(
			'tid' => Assign($params['tid'], 0), 
			'at' => Assign($params['uid'], 0),
		);

		$buffer_host = StorageFindID($schema, Assign($params['tid'], 0));
		if (is_array($buffer_host) and empty($buffer_host) == FALSE) {
			if ($params['uid'] == $buffer_host['ofuser']) {
				StorageEditByID($schema, array('isnewmsg' => 0, 'isnewat' => 0), $params['tid']);
			}
		}
		StorageEdit($schema . '_list', array('isnew' => 0, 'isnewat' => 0), $filter0);
		StorageEdit($schema . '_list', array('isnew' => 0, 'isnewat' => 0), $filter1);

		## 取消點贊狀態
		RefreshMsgByCDZ($buffer_host['ofuser'], 4, 0);
		RefreshMsgByCDZ($buffer_host['ofuser'], 4, 1);
		RefreshMsg(Assign($buffer_host['ofuser'], 0));
	}

	## 清除點贊狀態
	StorageEditByID('hh_zhaopin', array('isnewdz' => 0), Assign($params['tid'], 0));
	RefreshMsgByCDZ($params['uid'], 4, 0);
	RefreshMsgByCDZ($params['uid'], 4, 1);
	RefreshMsg(Assign($params['uid'], 0));
}

