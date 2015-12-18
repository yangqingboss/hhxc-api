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
$word_id = GetAnliKeyword($content);

if ($word_id == '0') {
} else {
}

if (CheckOpenID($params['openid'], $params['uid']) == TRUE) {

