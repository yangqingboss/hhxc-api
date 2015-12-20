<?php
// Copyright 2015 The Haohaoxiuche Team Authors. All right reserved.
// Use of this source that is governed by a Apache-style
// license that can be found in the LICENSE file.
//
// 技術版API編號104 查案例之獲取收藏案例 #代替034 
//
// @authors hjboss <hongjiangproject@yahoo.com> 2015-12-18#
// @version 1.0.0
// @package hhxc
if (!defined('HHXC')) die('Permission denied');

if (CheckOpenID($params['openid'], $params['uid']) == FALSE and FALSE) {
	$result['msg'] = MESSAGE_WARNING;
} else {
	$result = array('code' => '101', 'data' => array()); 
	$caseid = Assign($params['caseid'], 0);
	$subsql = 'SELECT ofurl FROM search_result WHERE id=ofanli';

	$condition = array(
		'schema' => 'hh_techuser_anli',
		'fields' => array(
			'id',
			'ofuser',
			'DATE(createdat) AS savetime',
			'(SELECT title FROM search_result WHERE id=ofanli) AS pheno',
			'(SELECT maintext FROM search_result WHERE id=ofanli) AS maintext',
			'search',
			'ofanli',
			'(SELECT havpic FROM search_result WHERE id=ofanli) AS havpic',
			'(SELECT words FROM search_result WHERE id=ofanli) AS words',
			"(SELECT title FROM search_result WHERE id=({$subsql})) AS fromurl",
		),
		'filter' => array(
			'id' => ($caseid > 0 ? array('LT', $caseid) : array('GT', $caseid)),
		),
	);
	if (empty($params['uid']) == FALSE) {
		$condition['filter']['ofuser'] = Assign($params['uid'], 0);
	}

	$recordset = StorageFind($condition);
	if (is_array($recordset) == FALSE or empty($recordset) == TRUE) {
		$result['msg'] = MESSAGE_EMPTY;
	} else {
		$url_anli = sprintf(PAGE_ANLI, $params['uid'], $params['openid'], DEBUG);

		foreach ($recordset as $index => $row) {
			if ($index == 0) {
				$result['databasever'] = $row['id'];
			}
			
			$item = array(
				'ofanli'   => $row['ofanli'],
				'search'   => $row['search'],
				'cid'      => $row['ofanli'],
				'caseid'   => $row['ofanli'],
				'savetime' => $row['savetime'],
				'title'    => $row['pheno'],
				'fromurl'  => fmtstr($row['fromurl']),
				'havpic'   => $row['havpic'],
				'words'    => $row['words'],
				'collect'  => '0',
				'caseurl'  => $url_anli . $row['id'],
			);

			$filter = array(
				'uid'  => Assign($params['uid'], 0),
				'tid'  => $item['cid'],
				'tag'  => '5',
				'type' => '1',
			);
			if (StorageCount('hh_techuser_shoucang', $filter)) {
				$item['collect'] = '1';
			}

			$result['data'][] = $item;
		}
	}
}
