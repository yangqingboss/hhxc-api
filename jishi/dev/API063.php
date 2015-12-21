<?php
// Copyright 2015 The Haohaoxiuche Team Authors. All right reserved.
// Use of this source that is governed by a Apache-style
// license that can be found in the LICENSE file.
//
// 技術版API編號063 匹配正時之獲取車型詳細信息
//
// @authors hjboss <hongjiangproject@yahoo.com> 2015-12-18#
// @version 1.0.0
// @package hhxc
if (!defined('HHXC')) die('Permission denied');

$the_times = CheckTimes(Assign($params['openid']), Assign($params['uid'], 0));
if (empty($the_times) == TRUE) {
	$condition = array(
		'schema' => 'hh_score',
		'fields' => array('chakan'),
		'filter' => array(
			'dengji' => "(SELECT grade FROM hh_techuser WHERE id='{$params['uid']}')",
		),
	);

	$record = StorageFindOne($condition);
	if (is_array($record) and empty($record) == FALSE) {
		$result = array('code' => '103', 'msg' => $record['chakan']);
	}

} else {
	$record = StorageFindID('car_type_zhengshi', Assign($params['id'], 0));

	if (is_array($record) == FALSE or empty($record) == TRUE) {
		$result['msg'] = '没有新数据，或者出现错误！';

	} else {
		$result = array(
			'code' => '101', 
			'url'  => sprintf(PAGE_ZHENGSHI, $params['uid'], $params['openid']) . $record['id'],
		);
	}
}

