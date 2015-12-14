<?php
// Copyright 2015 The Haohaoxiuche Team Authors. All right reserved.
// Use of this source that is governed by a Apache-style
// license that can be found in the LICENSE file.
//
// 好好修車手機網頁版 新聞內容模塊
//
// @authors hjboss <hongjiangproject@yahoo.com> 2015-12-14#
// @version 1.0.0
// @package hhxc
$mysql = StorageConnect(DB_HOST, DB_USER, DB_PWD, DB_NAME, DB_CHARSET);
$news = StorageQueryByID('news', $_REQUEST['id']);
$content = str_replace('/ueditor/php/upload', 'http://goviewtech.com/ueditor/php/upload', $news['content']);
echo <<<EOD
<div class="h-news-content">{$content}</div>
EOD;

