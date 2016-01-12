<?php
// Copyright 2015 The Haohaoxiuche Team Authors. All right reserved.
// Use of this source that is governed by a Apache-style
// license that can be found in the LICENSE file.
//
// 技術版APP 分享APP頁面
//
// @authors hjboss <hongjiangproject@yahoo.com> 2015-01-02#
// @version 1.0.0
// @package hhxc
define('HHXC', TRUE);
require_once('common.php');
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
.h-share-content {background-color:#e6e6e6;color:#323232;min-height:15em;padding:1em 2em;}
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
.h-top-common {
	background-color: #e6e6e6;
	margin-bottom: 1em;
	padding: 1em 0.5em;
}
.h-top-common .h-left {
	float: left;
}
.h-top-common .h-right {
	float: right;
}
.h-top-common .h-clear {
	clear: both;
}
.h-top-common .h-left, .h-top-common .h-right {
	width: 49%;
	text-align: center;
}
.h-top-common .h-text {
	margin-top: 0.5em;
}
.h-top-common .h-text strong {
	color: #d01109;
}
.h-top-one {
	background: #e6e6e6 url('<?php echo URL_MOBILE;?>/images/pk.png') center no-repeat;
	height: 85px;
}
.h-top-one .h-left, .h-top-one .h-right {
	line-height: 60px;
	font-size: 24px;
	width: 35%;
}
.h-top-one strong {
	font-size: 30px;
}
@media screen and (max-width: 767px) {
	.h-huodong h1 {font-size:14px;font-weight:bold; line-height: 2.5em}
	.h-header {padding-left:0.75em; padding-right:0.75em}
	.navbar-brand {font-size:16px;}
	.h-top-common img {height: 108px}
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
			<a class="navbar-brand h-icon-button" style="background:url(<?php echo URL_MOBILE;?>/images/btn-download.png) right top no-repeat">永久免费</a>
			<!--<div style="clear:both"></div>-->
		</div>
	</div>
</nav>
<h4 style="padding:0 1em">软件功能截图：查故障码 查维修案例 图文并茂</h4>
<div class="container-fluid h-picture h-download" style="padding-top: 0 !important">
	<div class="row">
		<div class="col-xs-12 col-sm-6" style="text-align:right">
			<a href="#">
				<img src="<?php echo URL_MOBILE;?>/images/share_left.png" 
					style="width:100%" 
				/>
			</a>
		</div>
		<div class="col-xs-12 col-sm-6" style="text-align:left">
			<a href="#">
				<img src="<?php echo URL_MOBILE;?>/images/share_right.png" 
					style="width:100%" 
				/>
			</a>
		</div>
	</div>
</div>
<div class="container-fluid h-picture h-download" style="padding-top: 0 !important">
	<div class="row">
		<div class="col-xs-6 col-sm-6" style="text-align:right">
			<a href="https://appsto.re/hk/pzy-9.i">
				<img src="<?php echo URL_MOBILE;?>/images/appios.png" 
					style="max-width:200px;width:100%" 
				/>
			</a>
		</div>
		<div class="col-xs-6 col-sm-6" style="text-align:left">
			<a href="http://www.haohaoxiuche.com/download/nendcode/hhxcjsb.apk">
				<img src="<?php echo URL_MOBILE;?>/images/anzapp.png" 
					style="max-width:200px;width:100%" 
				/>
			</a>
		</div>
	</div>
</div>
<div class="h-content" style="padding-bottom:0">
	<div class="h-top-common h-top-one">
		<div class="h-left"><strong>老</strong>汽修人</div>
		<div class="h-right"><strong>新</strong>汽修人</div>
		<div class="h-clear"></div>
	</div>
	<div class="h-top-common">
		<div class="h-left"><img src="<?php echo URL_MOBILE;?>/images/11.png" height="200" /></div>
		<div class="h-right"><img src="<?php echo URL_MOBILE;?>/images/12.png" height="200" /></div>
		<div class="h-clear"></div>
		<div class="h-text">
上网找？慢！<strong>来好好修车找汽修案例</strong>，百度上有的，我们都有，还更专业更准确更快速！
		</div>
	</div>
	<div class="h-top-common">
		<div class="h-left"><img src="<?php echo URL_MOBILE;?>/images/21.png" height="200" /></div>
		<div class="h-right"><img src="<?php echo URL_MOBILE;?>/images/22.png" height="200" /></div>
		<div class="h-clear"></div>
		<div class="h-text">
跟着老师傅学？难！<strong>来好好修车学故障分析</strong>，二十年百万技师修车经验，毫无保留 免费传授！
		</div>
	</div>
	<div class="h-top-common">
		<div class="h-left"><img src="<?php echo URL_MOBILE;?>/images/31.png" height="200" /></div>
		<div class="h-right"><img src="<?php echo URL_MOBILE;?>/images/32.png" height="200" /></div>
		<div class="h-clear"></div>
		<div class="h-text">
QQ群里问？乱！<strong>来好好修车汽修人论坛</strong>，三百万维修技师专业专属线上聚集地，汽修人的朋友圈！
		</div>
	</div>
</div>
<div class="container-fluid h-picture h-download" style="padding-top: 0 !important">
	<div class="row">
		<div class="col-xs-6 col-sm-6" style="text-align:right">
			<a href="https://appsto.re/hk/pzy-9.i">
				<img src="<?php echo URL_MOBILE;?>/images/appios.png" 
					style="max-width:200px;width:100%" 
				/>
			</a>
		</div>
		<div class="col-xs-6 col-sm-6" style="text-align:left">
			<a href="http://www.haohaoxiuche.com/download/nendcode/hhxcjsb.apk">
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
