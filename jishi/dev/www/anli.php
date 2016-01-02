<?php
// Copyright 2015 The Haohaoxiuche Team Authors. All right reserved.
// Use of this source that is governed by a Apache-style
// license that can be found in the LICENSE file.
//
// 技術版APP 案例分析詳細頁面
//
// @authors hjboss <hongjiangproject@yahoo.com> 2016-01-02#
// @version 1.0.0
// @package hhxc
define('HHXC', TRUE);
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'common.php');

$condition = array(
	'schema' => 'search_result',
	'fields' => array(
		'*',
		'(SELECT maintext FROM search_result_maintext WHERE resultid=search_result.id) AS maintext',
		'(SELECT title    FROM search_object WHERE id=search_result.ofurl) AS h_fromurl',
	),
	'filter' => array(
		'id' => $_GET['resultid'],
	),
);
$anli = StorageFindOne($condition);
$anli['maintext'] = trim($anli['maintext'], '<BRbr />');
$anli['maintext'] = str_replace('<BR><BR>', '</p><p>', $anli['maintext']);
$anli['maintext'] = str_replace('<br><br>', '</p><p>', $anli['maintext']);

$url_download = 'http://www.haohaoxiuche.com/download/hhxcjsb.apk';
if ($_REQUEST['t'] != 1) $url_download = 'https://appsto.re/hk/pzy-9.i';

header('Content-Type: text/html;charset=utf-8');
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
<title>好好修车-免费修车老师,私人汽车医生！</title>
<link rel="stylesheet" type="text/css" href="http://apps.bdimg.com/libs/bootstrap/3.3.4/css/bootstrap.min.css" />
<link rel="stylesheet" type="text/css" href="<?php echo URL_MOBILE;?>/style.css" />
<link rel="stylesheet" type="text/css" href="<?php echo URL_MOBILE;?>/anli-info.css" />
<style type="text/css">
.g-content br {
	display: block !important;
	margin: 2em !important;
}
h-home {
	background-color: #ffffff;
	padding: 0.75em 0 !important;
	margin-bottom: 10px; 
}
.h-home h4 {
	text-align: center;
	color: #808080;
	font-weight: bold;
}
.h-home h4.h-title {
	font-weight: normal;
	color: #262626;
}
.h-home .h-picture {
	padding: 1.5em 0;
}
.h-home p {
	text-align: center;
	color: #939393;
}
.h-home p.h-first {
	margin-bottom: 3em;
}
.h-anli-title {
	font-size: 20px;
	color: #d01109;
	line-height:1.25em;
	margin-top: 1.25em;
}
.h-anli-info {
	color: #d01109;
	font-size: 14px;
	margin-bottom: 1.25em;
}
.h-info-left {
	float: left;
}
.h-info-right {
	float: right;
}
.navbar-brand {
	font-weight: bold;
	padding-left: 2.5em;
	color:#ffffff !important;
	background:url('<?php echo URL_MOBILE;?>/images/logo.png') no-repeat left center !important;
}
.h-icon-button {
	line-height: 48px;
	position:absolute;
	right: 1em;
	display: block !important;
}
.h-header {
	background:#d01109;
	border: 0;
	border-radius: 0;
	padding-left: 1em;
	padding-right: 1em;
	margin: -0.75em;
}
.h-bottom {
	position: fixed;
	bottom: 0;
	display: bold;
	width: 100%;
	margin: -0.75em -0.75em 0 -0.75em;
	background-color: #eeeeee !important;
}
.h-bottom .navbar-brand {
	background:url('<?php echo URL_MOBILE;?>/images/logo-bottom.png') no-repeat left center !important;
	color: #353535 !important;
	font-size: 18px;
	line-height: 1.25em;
	margin-bottom: 0.125em;
}
.h-bottom .navbar-brand span {
	display: block;
	padding-left: 8px;
	margin-top: -0.5em;
} 
.h-bottom .navbar-brand .h-em {
	font-size: 14px;
	color: #b3b3b3;
	margin-top: -0.125em;
}
</style>
</head>
<body>
<?php if ($_REQUEST['share']): ?>
<nav class="navbar navbar-default container-fluid h-header">
	<div class="container-fluid">
		<div class="navbar-header">
			<a class="navbar-brand" href="#">有修车难题，找好好修车！</a>
			<a href="<?php echo URL_MOBILE;?>/index.php/home" class="h-icon-button">
				<img src="<?php echo URL_MOBILE;?>/images/top_buttom.png" height="36" />
			</a>
			<!--<div style="clear:both"></div>-->
		</div>
	</div>
</nav>
<?php endif;?>
<h1 class="h-anli-title"><?php echo $anli['title'];?></h1>
<div class="h-anli-info">
	<div class="h-info-left">来源：<?php echo $anli['h_fromurl'];?></div>
	<div class="h-info-right">
		<?php if($anli['havpic']) echo '图/';?>
		文 <?php echo $anli['words'];?>字
	</div>
	<div style="clear:both;"></div>
</div>
<div class="g-app g-content" id="g-content" data-id="<?php echo $anli['id'];?>" data-url="<?php echo URL_API;?>">
	<p><?php echo $anli['maintext'];?></p>
</div>
<small class="g-app g-link">本文由好好修车转码以便移动设备阅读<a class="g-button" data-id="0">查看原文</a></small>
<div class="g-app g-pinlun">
	<h4>最新评论</h4>
	<em id="g-message">正在加载！！！</em>
	<em id="g-empty" style="display:none">暂无评论！！！</em>
	<ul id="g-list"></ul>
</div>
<?php if ($_REQUEST['share']): ?>
<nav class="navbar navbar-default container-fluid h-bottom">
	<div class="container-fluid">
		<div class="navbar-header">
			<a class="navbar-brand" href="#">
				<span>百度能查到的汽修案例我们都有</span>
				<span class="h-em">还更快速更精确更专业</span>
			</a>
			<a href="<?php echo $url_download;?>" class="h-icon-button">
				<img src="<?php echo URL_MOBILE;?>/images/download.png" height="42" />
			</a>
			<!--<div style="clear:both"></div>-->
		</div>
	</div>
</nav>
<?php endif;?>
<div id="content-old" style="position:absolute;width:100%;height:100%;top:0;left:0;display:none">
	<nav class="navbar navbar-default navbar-fixed-bottom" style="background:rgba(248,248,248,0.5)">
		<div class="container">
			<button type="button" class="btn btn-info navbar-btn btn-block g-button" data-id="1">
			返回
			</button>
		</div>
	</nav>
	<iframe id="g-iframe" data-url="<?php echo $anli['url'];?>" class="embed-responsive-item"
		style="width:100%;height:100%"
	></iframe>
</div>
<script type="text/javascript" src="http://apps.bdimg.com/libs/jquery/1.11.3/jquery.min.js"></script>
<script type="text/javascript">
;(function(){
	var url = jQuery('#g-content').attr('data-url'), cid = jQuery('#g-content').attr('data-id');
	jQuery.post(url, {'data': '{"code":"107","cid":"'+cid+'"}'}, function(response){
		jQuery('#g-message').hide();

		var buffers = jQuery.parseJSON(response);
		if (buffers['data'].length == 0) {
			jQuery('#g-empty').show();
			return;
		}

		jQuery('#g-empty').hide();var html = '';
		for (var index = 0; index < buffers['data'].length; index++) {
			html += '<li><div class="g-pinlun-header">'
			html += '<img src="' + buffers['data'][index]['image'] + '" />';
			html += '<h5>' + buffers['data'][index]['nick'] + '</h5>';
			html += '<small>' + buffers['data'][index]['potimes'] + '</small>';
			html += '</div><div class="g-pinlun-body">' + buffers['data'][index]['context'];
			html += '</div></li>';
		}
		jQuery('#g-list').html(html);
	});
	
	jQuery('.g-button').click(function() {
		var status = jQuery(this).attr('data-id');
		if (status == 0) {
			jQuery('.g-app').hide();
			jQuery('#content-old').show();
			if (!jQuery('#g-iframe').attr('src')) {
				jQuery('#g-iframe').attr('src', jQuery('#g-iframe').attr('data-url'));
			}
		} else {
			jQuery('.g-app').show();
			jQuery('#content-old').hide();
		}
	});
})();
</script>
</body>
</html>
