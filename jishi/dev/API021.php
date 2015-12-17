<?php
// Copyright 2015 The Haohaoxiuche Team Authors. All right reserved.
// Use of this source that is governed by a Apache-style
// license that can be found in the LICENSE file.
//
// 技術版API編號021 查现象-获取现象数据
//
// @authors hjboss <hongjiangproject@yahoo.com> 2015-12-16#
// @version 1.0.0
// @package hhxc
if (!defined('HHXC')) die('Permission denied');

$content = SpeechWords(Assign($params['content']));
$word_id = explode(',', GetKeyword($content));

## 構建現象查詢SQL
$condition_sub = array(
	'schema' => array('car_symptom', 'car_symptom_fenxi', 'car_word'),
	'fields' => array(
		'resultid',
		'keyid',
		'title',
		'miaoshu',
		'lable',
		'(CASE oftype WHEN 3 THEN 100 WHEN 4 THEN 80 WHEN 5 THEN 60 WHEN 1 THEN 100 ELSE 10 END) AS wordtype',
		'SUM((1-text_step))*20+score AS fenshu',
	),
	'filter' => array(
		't1.resultid' => 't0.id',
		't1.keyid'    => 't2.id',
		'keyid'       => array('IN', $word_id),
	),
	'others' => 'GROUP BY resultid, keyid',
);
$condition = array(
	'schema' => '(' . SQLSub($condition_sub) . ') AS tb',
	'fields' => array(
		'resultid AS id',
		'title',
		'miaoshu',
		'lable',
		'sum(wordtype + fenshu) AS score',
	),
	'others' => 'GROUP BY resultid ORDER BY score DESC',
);

## 統計查詢總數
$count = StorageRows($condition);

## 截獲最新20條數據
$condition['others'] .= ' LIMIT 20';
$recordset = StorageFind($condition);
if (is_array($recordset) and empty($recordset) == FALSE) {
	$result = array('code' => '101', 'data' => array());

	$item = array(
		'total' => $count,
		'list'  => array(),
		'keys'  => array(),
	);

	foreach ($recordset as $index => $row) {
		$item['list'][] = array(
			'id'      => $row['id'],
			'title'   => fmtstr($row['title']),
			'lable'   => fmtstr($row['lable']),
			'miaoshu' => fmtstr($row['miaoshu']),
		);
	}

	$condition_buf = array(
		'schema' => 'car_word',
		'filter' => array(
			'id' => array('IN', $word_id),
		),
	);

	$buf = StorageFind($condition_buf);
	if (is_array($buf) and empty($buf) == FALSE) {
		foreach ($buf as $row) {
			$item['keys'][] = $row['keyword'];

			for ($index = 1; $index <= 9; $index++) {
				if (empty($row['keyword' . $index]) == FALSE) {
					$item['keys'][] = $row['keyword' . $index];
				}
			}
		}
	}

	$result['data'][] = $item;

	## 積累積分
	if (CheckOpenID($params['openid'], $params['uid']) == TRUE) {
		Techuser_setScore($params['uid'], 2);
	}
}
