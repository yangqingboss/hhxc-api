<?php
// Copyright 2015 The Haohaoxiuche Team Authors. All right reserved.
// Use of this source that is governed by a Apache-style
// license that can be found in the LICENSE file.
//
// 技術版API編號008 發送正在解決問題的回復
//
// @authors hjboss <hongjiangproject@yahoo.com> 2015-12-14#
// @version 1.0.0
// @package hhxc
if (!defined('HHXC')) die('Permission denied');

if (CheckOpenID($params['openid'], $params['uid']) == FALSE) {
	$result['msg'] = MESSAGE_WARNING;
} else {
	$data = array(
		'uid'     => Assign($params['uid'], 0),
		'qid'     => Assign($params['qid'], 0),
		'content' => Assign($params['content']),
		'pubtime' => 'NOW()',
		'no'      => "(SELECT maxno FROM hh_questions WHERE id='{$params['qid']}')+1",
		'type'    => 1,
	);

	$id = StorageAdd('hh_questions_list', $data);
	if (empty($id) == TRUE) {
		$result['msg'] = '发送失败！';
	} else {
		$result = array('code' => '101', 'data' => array());
		StorageEdit('hh_questions', array('maxno' => 'maxno+1'), array('id' => $params['qid']));

		$buf = StorageFindID('hh_questions_list', $id);
		if (is_array($buf) and empty($buf) == FALSE) {
			$result['data'][] = array(
				'posttime' => $buf['pubtime'],
				'index'    => $buf['no'],
			);
		}
	}
}
