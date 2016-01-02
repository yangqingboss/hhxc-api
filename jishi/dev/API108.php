<?php
// Copyright 2015 The Haohaoxiuche Team Authors. All right reserved.
// Use of this source that is governed by a Apache-style
// license that can be found in the LICENSE file.
//
// 技術版API編號108 認證（上傳圖片）
//
// @authors hjboss <hongjiangproject@yahoo.com> 2015-12-18#
// @version 1.0.0
// @package hhxc
if (!defined('HHXC')) die('Permission denied');


if (CheckOpenID($params['openid'], $params['uid']) == FALSE) {
	$result['msg'] = MESSAGE_WARNING;
} else {
	$condition = array(
		'schema' => 'hh_identification',
		'fields' => '*',
		'filter' => array(
			'uid' => Assign($params['uid'], 0),
		),
	);
	$record = StorageFindOne($condition);

	## 處理上傳圖片
	$fileids = array();
	$filekeys = array('fileid', 'filerz1', 'filerz2');
	foreach ($filekeys as $number => $key) {
		$tmpname  = "{$params['uid']}-{$number}-" . time() . ".png";
		
		if ($_FILES[$key]['error'] <= 0 and empty($_FILES[$key]['tmp_name']) == FALSE) {
			$savepath = PIC_D_PATH . DIRECTORY_SEPARATOR . $tmpname;
			$fileids[$key] = $tmpname;
			move_uploaded_file($_FILES[$key]['tmp_name'], $savepath);
		}
	}

	if (is_array($record) == FALSE or empty($record) == TRUE) {
		$data = array(
			'uid'            => Assign($params['uid'], 0),
			'name'           => Assign($params['name']),
			'identification' => Assign($params['identification']),
			'fileid'         => Assign($fileids['fileid']),
			'filerz1'        => Assign($fileids['filerz1']),
			'filerz2'        => Assign($fileids['filerz2']),
			'deviceid'       => Assign($params['deviceid']),
			'status'         => 1,
		);
		$id = StorageAdd('hh_identification', $data);
	} else {
		$data = array(
			'name'           => Assign($params['name']),
			'identification' => Assign($params['identification']),
			'deviceid'       => Assign($params['deviceid']),
		);
		if ($fileids['fileid'])  $data['fileid']  = $fileids['fileid'];
		if ($fileids['filerz1']) $data['filerz1'] = $fileids['filerz1'];
		if ($fileids['filerz2']) $data['filerz2'] = $fileids['filerz2'];

		$num = StorageEdit('hh_identification', $data, array('uid' => Assign($params['uid'], 0)));
	}

	$result = array('code' => '101', 'msg' => MESSAGE_SUCCESS);
}

