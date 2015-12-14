<?php
// Copyright 2015 The Haohaoxiuche Team Authors. All right reserved.
// Use of this source that is governed by a Apache-style
// license that can be found in the LICENSE file.
//
// 好好修車手機網頁版 公共頁面模塊
//
// @authors hjboss <hongjiangproject@yahoo.com> 2015-12-14#
// @version 1.0.0
// @package hhxc
define('HHXC',        TRUE);
define('API_ROOT',    dirname(dirname(__FILE__)));
define('API_SELF',    basename($_SERVER['PHP_SELF']));
define('API_VERSION', '1.8.0');

require_once(API_ROOT . DIRECTORY_SEPARATOR . 'config.mobile.php');
require_once(API_ROOT . DIRECTORY_SEPARATOR . 'common.php');

## 主導航項ACTIVE狀態
$script = basename($_SERVER['PHP_SELF']); $nav_active = array();
switch ($script) {
case 'download':
	$nav_active['download'] = 'active';
	break;

case 'weixin':
	$nav_active['weixin'] = 'active';
	break;

case 'resource':
	$nav_active['resource'] = 'active';
	break;

default:
	$nav_active['home'] = 'active';
}

header('Content-Type: text/html; charset=UTF-8');
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
<title>好好修车-免费修车老师,私人汽车医生！</title>
<link rel="stylesheet" type="text/css" href="http://apps.bdimg.com/libs/bootstrap/3.3.4/css/bootstrap.min.css" />
<link rel="stylesheet" type="text/css" href="style.css" />
</head>
<body>
<nav class="navbar navbar-default container-fluid h-header">
	<ul class="nav navbar-nav row">
		<li class="<?php echo $nav_active['home'];?> col-xs-3 col-sm-3">
			<a href="index.php">首页</a>
		</li>
		<li class="<?php echo $nav_active['download'];?> col-xs-3 col-sm-3">
			<a href="index.php/download">APP下载</a>
		</li>
		<li class="<?php echo $nav_active['weixin'];?> col-xs-3 col-sm-3">
			<a href="index.php/weixin">关注微信</a>
		</li>
		<li class="<?php echo $nav_active['resource'];?> col-xs-3 col-sm-3">
			<a href="index.php/resource">资料下载</a>
		</li>
	</ul>
</nav>
<?php include_once($script . '.php'); ?>
<footer class="h-footer">
	<nav class="navbar navbar-default">
		<ul class="nav navbar-nav">
			<li><a href="index.php/about">关于好好修车</a></li>
			<li><a class="sep">|</a></li>
			<li><a href="index.php/privacy">隐私条款</a></li>
			<li><a class="sep">|</a></li>
			<li><a href="index.php/service">服务协议</a></li>
			<li><a class="sep">|</a></li>
			<li><a href="index.php/contact">联系我们</a></li>
		</ul>
	</nav>
	<p><small>好好修车&nbsp;&nbsp;&copy;2015深圳市观微科技有限公司版权所有</small></p>
	<p><small>粤ICP备15037541号-2</small></p>
</footer>
</body>
</html>
