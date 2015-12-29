<?php
// Copyright 2015 The Haohaoxiuche Team Authors. All right reserved.
// Use of this source that is governed by a Apache-style
// license that can be found in the LICENSE file.
//
// 技術版API編號077 車修人之發表新貼（上傳圖片）
//
// @authors hjboss <hongjiangproject@yahoo.com> 2015-12-18#
// @version 1.0.0
// @package hhxc
if (!defined('HHXC')) die('Permission denied');

if (CheckOpenID($params['openid'], $params['uid']) == FALSE) {
	$result['msg'] = MESSAGE_WARNING;
} else {
	$data = array(
		'pubuser'   => Assign($params['uid'], 0),
		'type'      => Assign($params['tag'], 0),
		'title'     => Assign($params['title']),
		'content'   => Assign($params['content']),
		'pubtime'   => 'NOW()',
		'anonymous' => Assign($params['anonymous'], 0),
		'reward'    => Assign($params['reward'], 0),
		'rewarded'  => empty($params['reward']) ? 0 : Assign($params['reward'], 0),
	);
	
	$id = StorageAdd('hh_techforum', $data);
	if (empty($id) == TRUE) {
		$result['msg'] = '发送失败！';
	} else {
		for ($index = 0; $index < 6; $index++) {
			$field = 'image' . strval($index + 1);

			if (empty($_FILES[$field])) {
				continue;
			}

			if ($_FILES[$field]['error'] <= 0) {
				
				$buf_data = array(
					'qid'       => $id,
					'createdat' => 'NOW()',
					'filename'  => $_FILES[$field]['name'],
					'size'      => $_FILES[$field]['size'],
				);
				$buf_id = StorageAdd('hh_techforum_img', $buf_data);

				$uploadfile   = PIC_F_PATH . DIRECTORY_SEPARATOR . "{$buf_id}.png";
				$uploadfile_s = PIC_F_PATH . DIRECTORY_SEPARATOR . "{$buf_id}_s.png";

				move_uploaded_file($_FILES[$field]['tmp_name'], $uploadfile);
				MakeSmallIMG($uploadfile, $uploadfile_s);
			}
		}

		$result = array('code' => '101', 'data' => array());

		$record = StorageFindID('hh_techforum', $id);
		if (is_array($record) and empty($record) == FALSE) {
			$result['data'][] = array(
				'posttime' => $record['pubtime'],
				'tid'      => $record['id'],
			);
		}
	}
}

