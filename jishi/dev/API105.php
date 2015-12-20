<?php
// Copyright 2015 The Haohaoxiuche Team Authors. All right reserved.
// Use of this source that is governed by a Apache-style
// license that can be found in the LICENSE file.
//
// 技術版API編號105 案例跟貼
//
// @authors hjboss <hongjiangproject@yahoo.com> 2015-12-18#
// @version 1.0.0
// @package hhxc
if (!defined('HHXC')) die('Permission denied');

if (CheckOpenID($params['openid'], $params['uid']) == FALSE) {
	$result['msg'] = MESSAGE_WARNING;
} else {
	$result = array('code' => '101', 'msg' => '提交成功！');
	
	$data = array(
		'uid'       => Assign($params['uid'], 0),
		'cid'       => Assign($params['cid'], 0),
		'tag'       => 1,
		'createdat' => date('Y-m-d H:i:s'),
		'content'   => Assign($params['content']),
	);
	$id = StorageAdd('hh_anli_thread', $data);
	if (empty($id) == TRUE) {
		$result['msg'] = '提交失败！';
	}
}
