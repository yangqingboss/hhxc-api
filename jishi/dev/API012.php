<?php
// Copyright 2015 The Haohaoxiuche Team Authors. All right reserved.
// Use of this source that is governed by a Apache-style
// license that can be found in the LICENSE file.
//
// 技術版API編號006 獲取汽修人主題詳細信息
//
// @authors hjboss <hongjiangproject@yahoo.com> 2015-12-16#
// @version 1.0.0
// @package hhxc
if (!defined('HHXC')) die('Permission denied');

if (empty(Assign($params['tid'])) == FALSE) {
	$condition = array(
		'schema' => 'hh_techforum_list',
		'fields' => array(
			'*',
			'(SELECT nick FROM hh_techuser WHERE id=pubuser) AS h_nick',
			'(SELECT headerimg FROM hh_techuser WHERE id=pubuser) AS h_headerimg',
		),
		'filter' => array(
			'tid' => Assign($params['tid'], 0),
			'no'  => array('GT' => Assign($params['index'], 0)),
		),
	);

	$recordset = StorageFind($condition);
	if (is_array($recordset) == FALSE or empty($recordset) == TRUE) {
		$result['msg'] = MESSAGE_EMPTY;
	} else {
	}
}

