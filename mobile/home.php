<?php
// Copyright 2015 The Haohaoxiuche Team Authors. All right reserved.
// Use of this source that is governed by a Apache-style
// license that can be found in the LICENSE file.
//
// 好好修車手機網頁版 首頁頁面模塊
//
// @authors hjboss <hongjiangproject@yahoo.com> 2015-12-12#
// @version 1.0.0
// @package hhxc
## 首頁最新新聞
$mysql = StorageConnect(DB_HOST, DB_USER, DB_PWD, DB_NAME, DB_CHARSET);
$news_list = StorageQuery('news', '*', array('type_new' => 1), 'ORDER BY date DESC LIMIT 5');

## 首頁廣告圖片
$pictures = array(
	array('src' => 'images/4.png', 'href' => 'index.php/download', 'title' => ''),
	array('src' => 'images/3.jpg', 'href' => 'index.php/home',     'title' => ''),
	array('src' => 'images/2.jpg', 'href' => 'index.php/home',     'title' => ''),
	array('src' => 'images/1.jpg', 'href' => 'index.php/home',     'title' => ''),
);
?>
<style type="text/css">body {background-color:#f8f8f8}</style>
<div class="carousel slide h-carousel" data-ride="carousel" id="carousel-picture-generic">
	<ol class="carousel-indicators">
		<?php foreach ($pictures as $number => $picture): ?>
		<li data-target="#carousel-picture-generic" data-slide-to="<?php echo $number;?>"
			class="<?php if ($number == 0) echo 'active';?>"
		></li>
		<?php endforeach; ?>
	</ol>
	<div class="carousel-inner" role="listbox">
		<?php foreach ($pictutres as $number => $picture): ?>
		<div class="item <?php if ($number == 0) echo 'active';?>">
			<a href="<?php echo $picture['href'];?>" target="_block">
				<img src="<?php echo $picture['src'];?>" alt="<?php echo $picture['title'];?>"
					style="width: 100%"
				/>
			</a>
			<div class="carousel-caption"><?php echo $picture['title'];?></div>
		</div>
		<?php endforeach; ?>
	</div>
	<a class="left carousel-control" href="#carousel-picture-generic" role="button" data-slide="prve">
		<span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
		<span class="sr-only">Previous</span>
	</a>
	<a class="right carousel-control" href="#carousel-picture-generic" role="button" data-slide="next">
		<span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
		<span class="sr-only">Next</span>
	</a>
</div>
<div class="h-home">
	<h4>点击以下按钮下载APP</h4>
	<div class="container-fluid h-picture">
		<div class="row">
			<div class="col-xs-6 col-sm-6" style="text-align:right">
				<a href="http://www.haohaoxiuche.com/download/hhxcjsb.apk">
					<img src="images/anzapp.png" style="max-width:200px;width:100%" />
				</a>
			</div>
			<div class="col-xs-6 col-sm-6" style="text-align:left">
				<a href="https://appsto.re/hk/pzy-9.i">
					<img src="images/appios.png" style="max-width:200px;width:100%" />
				</a>
			</div>
		</div>
	</div>
</div>
<div class="h-home">
	<h4>好好修车，免费修车老师</h4>
	<div class="container-fluid h-picture">
		<div class="row">
			<div class="col-xs-6 col-sm-6" style="text-align:center">
				<a href="">
					<img src="./images/fenxi.png" style="max-width:100px;width:100%;" />
				</a>
				<h4 class="h-title">故障分析</h5>
				<p class="h-first">尽可能多的分析故障可能性</p>
			</div>
			<div class="col-xs-6 col-sm-6" style="text-align:center">
				<a href="">
					<img src="./images/anli.png" style="max-width:100px;width:100%;" />
				</a>
				<h4 class="h-title">汽修案例</h4>
				<p class="h-first">互联网修车案例都在这里</p>
			</div>
			<div class="col-xs-6 col-sm-6" style="text-align:center">
				<a href="">
					<img src="images/guzhang.png" style="max-width:100px;width:100%" />
				</a>
				<h4 class="h-title">查询故码</h4>
				<p>详尽描述和最佳解决办法</p>
			</div>
			<div class="col-xs-6 col-sm-6" style="text-align:center">
				<a href="">
					<img src="images/peidui.png" style="max-width:100px;width:100%" />
				</a>
				<h4 class="h-title">匹配正时</h4>
				<p>各种修车实用手册和工具</p>
			</div>
		</div>
	</div>
</div>
<div class="h-home h-news">
	<h4>最新消息</h4>
	<?php foreach ($news_list as $number => $news): ?>
	<div class="h-news-item">
		<img src="images/news.png" style="width: 36px" />
		<span>
			<a href="content.php?id=<?php echo $news['id'];?>"><h5><?php echo $news['title'];?></h5></a>
			<span class="h-date"><?php echo $news['date'];?></span>
		</span>
	</div>
	<?php endforeach; ?>
</div>
<script type="text/javascript" src="http://apps.bdimg.com/libs/jquery/1.10.2/jquery.min.js"></script>
<script type="text/javascript" src="http://apps.bdimg.com/libs/bootstrap/3.3.4/js/bootstrap.min.js"></script>
