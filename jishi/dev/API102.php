<?php
// Copyright 2015 The Haohaoxiuche Team Authors. All right reserved.
// Use of this source that is governed by a Apache-style
// license that can be found in the LICENSE file.
//
// 技術版API編號102 查案例-獲取案例數據 ##代替57
//
// @authors hjboss <hongjiangproject@yahoo.com> 2015-12-18#
// @version 1.0.0
// @package hhxc
if (!defined('HHXC')) die('Permission denied');

$content = SpeechWords(Assign($params['content']));
$word_id = explode(',', GetAnliKeyword($content));
$result_count = 0;

if ($word_id == '0') {
	$result['msg'] = '无数据！';
	die(JsonEncode($result));
} else {
	$result = array('code' => '101', 'data' => array('keys' => array(), 'list' => array()));

	$condition = array(
		'schema' => 'car_word',
		'filter' => array(
			'id' => array('IN', $word_id),
		),
	);

	$recordset = StorageFind($condition); $ofurl = '';
	foreach ($recordset as $row) {
		$result['data']['keys'][] = $row['keyword'];

		for ($index = 1; $index <= 9; $index++) {
			if (empty($row["keyword{$index}"]) == FALSE) {
				$result['data']['keys'][] = $row["keyword{$index}"];
			}
		}
	}

	$anli = json_decode(Charset('[' . GetAnliList(join($word_id, ',')) . ']', DB_CHARSET, CL_CHARSET), TRUE);
	if (is_array($anli) and empty($anli) == FALSE) {
		foreach ($anli as $index => $row) {
			$item = array(
				'cid'     => $row['id'],
				'title'   => $row['title'],
				'havpic'  => '0',
				'words'   => '0',
				'collect' => '0',
			);

			$condition_sub = array(
				'schema' => 'search_result',
				'fields' => array(
					'havpic',
					'words',
					'(SELECT title FROM search_object WHERE id=ofurl) AS fromurl',
				),
				'filter' => array(
					'id' => $item['cid'],
				),
			);

			$buf = StorageFind($condition_sub);
			if (is_array($buf) and empty($buf) == FALSE) {
				foreach ($buf as $index => $row) {
					$item['havpic']  = "{$row['havpic']}";
					$item['words']   = "{$row['words']}";
					$item['fromurl'] = "{$row['fromurl']}";
					$ofurl = $row['ofurl'];
				}
			}

			$filter = array(
				'tid'  => Assign($params['tid'], 0),
				'uid'  => Assign($params['uid'], 0),
				'tag'  => 5,
				'type' => 1,
			);
			if (StorageCount('hh_techuser_shoucang', $filter)) {
				$item['collect'] = '1';
			}

			$result['data']['list'][] = $item;
			$result_count++;
		}
	}
}

if (CheckOpenID($params['openid'], $params['uid']) == TRUE) {
	Techuser_search(Assign($params['uid'], 0), $content, 2, $word_id, $result_count);
}
