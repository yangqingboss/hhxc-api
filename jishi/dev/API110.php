<?php
// Copyright 2015 The Haohaoxiuche Team Authors. All right reserved.
// Use of this source that is governed by a Apache-style
// license that can be found in the LICENSE file.
//
// 技術版API編號110 懸賞
//
// @authors hjboss <hongjiangproject@yahoo.com> 2015-12-18#
// @version 1.0.0
// @package hhxc
if (!defined('HHXC')) die('Permission denied');

if (CheckOpenID($params['openid'], $params['uid']) == FALSE) {
	$result['msg'] = MESSAGE_WARNING;
} else if ($params['reward'] <= 1) {
	$result['msg'] = '该回复已经被采纳！';
} else {
	$info_tid   = StorageFindID('hh_techforum_list', Assign($params['tolistid'], 0));
	$user_uid   = StorageFindID('hh_techuser', Assign($params['uid'], 0));
	$user_touid = StorageFindID('hh_techuser', Assign($params['touid'], 0));

	## 設置回帖的採納狀態
	$fields_tolist = array(
		'adopt' => 1,
	);
	StorageEditByID('hh_techforum_list', $fields_tolist, Assign($params['tolistid'], 0));

	## 添加會貼者之可兌換積分
	$message = sprintf(
		RANKSCORE_ADOPTED,
		$info_tid['title'],
		SafeUsername($user_uid), 
		Techuser_viewRankScore($params['reward'])
	);
	//Techuser_setRankscore(Assign($params['touid'], 0), $params['reward'], $message, TRUE);
	Techuser_setRankByScore(Assign($params['touid'], 0), Techuser_viewRankScore($params['reward']), $message);

	## 設置主題悬赏狀態
	$fields_tid = array(
		'rewarded' => 1,
	);
	StorageEditByID('hh_techforum', $fields_tid, Assign($params['tid'], 0));

	## 記錄樓主採納日誌
	$message = sprintf(RANKSCORE_ADOPT, SafeUsername($user_touid), Techuser_viewRankScore($params['reward']));
	Techuser_setRankscore(Assign($params['uid'], 0), 0, $message);

	## 添加回帖者經驗值
	Techuser_setRank($params['touid'], 2);

	$result = array('code' => '101', 'msg' => MESSAGE_SUCCESS);

}
