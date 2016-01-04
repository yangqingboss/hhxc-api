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
	$rankscore = Techuser_rank2score(Assign($params['reward'], 0));
	if (empty($rankscore) == FALSE) {
		$record_user = StorageFindID('hh_techuser', $params['uid']);
		if (is_array($record_user) == FALSE or empty($record_user) == TRUE) {
			$result['msg'] = '当前帐号不存在！';
		} else if ($record_user['rankscore'] < $rankscore) {
			$result['msg'] = '您的可兑换积分余额不足！';
		} else {
			$result['msg'] = '';
		}
	}

	if (empty($result['msg']) == TRUE or empty($params['reward']) == TRUE) {
		$data = array(
			'pubuser'   => Assign($params['uid'], 0),
			'type'      => Assign($params['tag'], 0),
			'title'     => Assign($params['title']),
			'content'   => Assign($params['content']),
			'pubtime'   => 'NOW()',
			'anonymous' => Assign($params['anonymous'], 0),
			'reward'    => Assign($params['reward'], 0),
			'rewarded'  => empty($params['reward']) ? 0 : $rankscore,
		);
	
		$id = StorageAdd('hh_techforum', $data);
		if (empty($id) == TRUE) {
			$result['msg'] = MESSAGE_ERROR;
		} else {
			for ($index = 0; $index < 6; $index++) {
				$field = 'image' . strval($index + 1);

				if ($_FILES[$field]['error'] <= 0 and empty($_FILES[$field]['tmp_name']) == FALSE) {
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

			## 设置用戶可兌換積分
			if (empty($rankscore) == FALSE) {
				$message = sprintf(RANKSCORE_ASK, $data['title'], $params['reward']);
				Techuser_setRankscore($params['uid'], 0 - $rankscore, $message);
			}
		}
	}
}

