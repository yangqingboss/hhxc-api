<?php
// Copyright 2015 The Haohaoxiuche Team Authors. All right reserved.
// Use of this source that is governed by a Apache-style
// license that can be found in the LICENSE file.
//
// 技術版APP 分享頁面公共頁面
//
// @authors hjboss <hongjiangproject@yahoo.com> 2015-12-14#
// @version 1.0.0
// @package hhxc
if (!defined('HHXC')) die('Permission denied');
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
<title>好好修车-免费修车老师,私人汽车医生！</title>
<link rel="stylesheet" type="text/css" href="http://apps.bdimg.com/libs/bootstrap/3.3.4/css/bootstrap.min.css" />
<link rel="stylesheet" type="text/css" href="<?php echo URL_MOBILE;?>/style.css" />
<style type="text/css">
.h-share-content {
	background-color:#e6e6e6;
	color:#323232;
	min-height:15em;
	padding:1em 2em;
}
.h-share-content h2 {
	font-size:20px;
}
.h-share-content h4 img {
	//vertical-align: text-bottom;
}
.h-share-content .h-inner {
	line-height: 1.75em;
}
.h-header .container-fluid, .h-bottom .container-fluid {
	padding-left: 0.25em;
	padding-right: 0.25em;
}
.navbar-brand {
	font-weight: bold;
	color:#ffffff !important;
	padding-left: 0.25em;
}
.h-brand-logo {
	padding-top: 4px;
	padding-left: 0.5em;
	padding-right: 0;
}
.h-icon-button {
	line-height: 48px;
	position:absolute;
	right: 0.25em;
	display: block !important;
}
.h-header {
	background:#d01109;
	border: 0;
	border-radius: 0;
	padding-left: 1em;
	padding-right: 1em;
	//margin: -0.75em;
}
.h-bottom {
	display: bold;
	width: 100%;
	margin: -0.75em -0.75em 0 -0.75em;
	background-color: #eeeeee !important;
}
.h-bottom .navbar-brand {
	color: #353535 !important;
	font-size: 18px;
	line-height: 1.25em;
	margin-bottom: 0.125em;
}
.h-bottom .navbar-brand span {
	display: block;
	font-size: 14px;
	padding-left: 0.25em;
	margin-top: -0.5em;
} 
.h-bottom .navbar-brand .h-em {
	font-size: 12px;
	color: #b3b3b3;
	margin-top: -0.125em;
}

@media screen and (max-width: 768px) {
	.h-huodong h1 {font-size:14px;font-weight:bold; line-height: 2.5em}
	.h-header {padding-left:0.75em; padding-right:0.75em}
	.navbar-brand {font-size:16px;}
}
</style>
</head>
<nav class="navbar navbar-default container-fluid h-header">
	<div class="container-fluid">
		<div class="navbar-header">
			<a class="navbar-brand h-brand-logo" href="#">
				<img src="<?php echo URL_MOBILE;?>/images/logo-bottom.png" height="42" />
			</a>
			<a class="navbar-brand" href="#">有修车难题，找好好修车！</a>
			<a href="<?php echo URL_MOBILE;?>/index.php/home" class="h-icon-button">
				<img src="<?php echo URL_MOBILE;?>/images/top_buttom.png" height="42" />
			</a>
			<!--<div style="clear:both"></div>-->
		</div>
	</div>
</nav>
<div class="h-content">
	<div class="h-share-content">
		<h4><?php echo $headerimg;?></h4>
		<h2><?php echo $title;?></h2>
		<div class="h-inner"><?php echo $content;?></div>
	</div>
</div>
<div class="container-fluid h-picture h-download">
	<div class="row">
		<div class="col-xs-6 col-sm-6" style="text-align:right">
			<a href="https://appsto.re/hk/pzy-9.i">
				<img src="<?php echo URL_MOBILE;?>/images/appios.png" 
					style="max-width:200px;width:100%" 
				/>
			</a>
		</div>
		<div class="col-xs-6 col-sm-6" style="text-align:left">
			<a href="http://www.haohaoxiuche.com/download/hhxcjsb.apk">
				<img src="<?php echo URL_MOBILE;?>/images/anzapp.png" 
					style="max-width:200px;width:100%" 
				/>
			</a>
		</div>
	</div>
</div>
<div class="h-news-content h-huodong h-bottom">
	<img src="<?php echo URL_MOBILE;?>//huodong/2015-12-23-1.png" style="max-width:420px"/>
	<h1>
		<span class="glyphicon glyphicon-star" aria-hidden="true"></span>
		二十年来互联网上的修车经验毫无保留免费查看！<br />
		<span class="glyphicon glyphicon-star" aria-hidden="true"></span>
		百度能查到的我们都有，还更专业更快速更准确！<br />
		<span class="glyphicon glyphicon-star" aria-hidden="true"></span>
		三百万技师的专属线上聚集区，汽修人的朋友圈！<br />
	</h1>
</div>
<body>
</body>
</html>

