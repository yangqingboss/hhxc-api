<?php
// Copyright 2015 The Haohaoxiuche Team Authors. All right reserved.
// Use of this source that is governed by a Apache-style
// license that can be found in the LICENSE file.
//
// 好好修車手機網頁版 資料下載模塊
//
// @authors hjboss <hongjiangproject@yahoo.com> 2015-12-12#
// @version 1.0.0
// @package hhxc
$mysql = StorageConnect(DB_HOST, DB_USER, DB_PWD, DB_NAME_S, DB_CHARSET);
$limit = 10; $start = empty($_REQUEST['page']) ? '1' : intval($_REQUEST['page']);
$file_list = StoragePage('file', '*', '', $page, $limit);
?>
<style type="text/css">body {background-color:#f8f8f8}</style>
<?php foreach ($file_list as $number => $file): ?>
<div class="h-content h-qiceh-content">
	<a href="http://www.haohaoxiuche.com/admin/hone/wenjian.php?m=<?php echo $file['f_url'];?>">
		<h4><?php echo $file['title']; ?></h4>
	</a>
	<p>介绍：<?php echo $file['htmla'];?><br />上传时间：<?php echo $file['timed']; ?></p>
</div>
<?php endforeach; ?>

