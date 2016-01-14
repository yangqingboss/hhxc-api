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

$condition = array(
	'schema' => 'hh_tuijian_code',
	'filter' => array(
		'code'      => Assign($_REQUEST['needcode']),
		'createdat' => date('Y-m-d'),
	),
);
if (empty($_REQUEST['needcode']) == FALSE) {
	$buffer = StorageFind($condition);
	if (is_array($buffer) and empty($buffer) == FALSE) {
		$fields = array('number' => 'number+1');
		StorageEdit($condition['schema'], $fields, $condition['filter']);
	} else {
		$data = array(
			'code'      => Assign($_REQUEST['needcode']),
			'createdat' => date('Y-m-d'),
			'number'    => 1,
		);
		StorageAdd($condition['schema'], $data);
	}
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
<title>好好修车-免费修车老师,私人汽车医生！</title>
<link rel="stylesheet" type="text/css" href="http://apps.bdimg.com/libs/bootstrap/3.3.4/css/bootstrap.min.css" />
<link rel="stylesheet" type="text/css" href="<?php echo URL_MOBILE;?>/style.css" />
<script type="text/javascript" src="http://apps.bdimg.com/libs/jquery/1.10.2/jquery.min.js"></script>
<script type="text/javascript" src="http://apps.bdimg.com/libs/bootstrap/3.3.4/js/bootstrap.min.js"></script>
<style type="text/css">
.h-header {padding-top: 0.75em;}
.h-header .col-xs-4, .h-header .col-xs-8 {
	padding-left: 0.25em;
	padding-right: 0.25em;
}
.h-header .col-xs-4 img {border: 1px #d01108 solid;}
.h-header .col-xs-8 h4 {font-weight:bold;font-size:20px;text-align:center;margin-top:0;margin-bottom:4px}
.h-header .col-xs-6 {padding-left:2px; padding-right:2px}
.h-content {background:#e6e6e6}
.h-info {padding: 1em}
.h-info h4 {font-weight:bold;}
.h-info .table {border:0 !important}
.h-info .table th {width:92px;padding: 8px 0;font-weight:normal;border:0 !important}
.h-info .table td {padding: 8px 0;border:0 !important}
.h-info .h-table th {color:#adadad;width:84px}
.h-info .h-table-inner {margin:0}
.h-info .h-table-inner th, .h-info .h-table-inner td {color:#333;padding:0}
.h-info .h-table-inner th {width:42px}
</style>
</head>
<body>
<div class="container-fluid h-header">
	<div class="row">
		<div class="col-xs-4 col-sm-4 col-md-4" style="text-align:center">
			<img src="<?php echo URL_MOBILE;?>/images/share_code_app/logo.png" style="width:100%;max-width:140px"/>
		</div>
		<div class="col-xs-8 col-sm-8 col-md-8">
			<h4>遇汽修难题 找好好修车</h4>
			<div class="container-fluid h-picture h-download" style="padding: 0 !important">
				<div class="row">
					<div class="col-xs-6 col-sm-6" style="text-align:right">
						<a href="https://appsto.re/hk/pzy-9.i">
							<img src="<?php echo URL_MOBILE;?>/images/share_code_app/download_ios_big.png" 
								style="max-width:250px;width:100%" 
							/>
						</a>
					</div>
					<div class="col-xs-6 col-sm-6" style="text-align:left">
						<a href="http://www.haohaoxiuche.com/download/hhxcjsb.apk">
							<img src="<?php echo URL_MOBILE;?>/images/share_code_app/download_android_big.png" 
								style="max-width:250px;width:100%" 
							/>
						</a>
					</div>
				</div>
			</div>
			<ul class="list-inline" style="margin-top:6px;margin-left:2px">
				<li><img src="<?php echo URL_MOBILE;?>/images/share_code_app/stat.png" style="width:20px" /></li>
				<li><img src="<?php echo URL_MOBILE;?>/images/share_code_app/stat.png" style="width:20px" /></li>
				<li><img src="<?php echo URL_MOBILE;?>/images/share_code_app/stat.png" style="width:20px" /></li>
				<li><img src="<?php echo URL_MOBILE;?>/images/share_code_app/stat.png" style="width:20px" /></li>
				<li><img src="<?php echo URL_MOBILE;?>/images/share_code_app/stat.png" style="width:20px" /></li>
			</ul>
			<h5>下载量 100000<strong>+</strong></h5>
		</div>
	</div>
</div>
<div class="h-content">
	<div id="carousel-example-generic-1" class="carousel slide" data-ride="carousel">
	</div>
</div>
<div class="h-info">
	<h4>好好修车已支持所有手机平台下载，百万技师应用，好评如潮，下载免费使用</h4>
	<hr />
	<h4>信息</h4>
	<table class="table h-table">
		<tr>
			<th>开发商</th>
			<td>Goviewtech Co.,Ltd</td>
		</tr>
		<tr>
			<th>类别</th>
			<td>工具</td>
		</tr>
		<tr>
			<th>更新日期</th>
			<td>
				<table class="table h-table-inner">
					<tr>
						<th>iOS:</th>
						<td>2015年12月22日</td>
					</tr>
					<tr>
						<th>安卓:</th>
						<td>2016年1月9日</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<th>版本</th>
			<td>
				<table class="table h-table-inner">
					<tr>
						<th>iOS:</th>
						<td>1.7.3</td>
					</tr>
					<tr>
						<th>安卓:</th>
						<td>1.9.0</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<th>大小</th>
			<td>
				<table class="table h-table-inner">
					<tr>
						<th>iOS:</th>
						<td>15.9MB</td>
					</tr>
					<tr>
						<th>安卓:</th>
						<td>6.32MB</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<th>兼容性</th>
			<td>
				<table class="table h-table-inner">
					<tr>
						<th>iOS:</th>
						<td>需要iOS 7.0或更高版本。与iPhone、iPad、iPod touch兼容。</td>
					</tr>
					<tr>
						<th>安卓:</th>
						<td>4.0系统以上99%的手机。</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
	<hr />
	<h4>功能</h4>
	<table class="table">
		<tr>
			<th>1) 故障分析：</th>
			<td>通过搜索或检索，输入故障现象精确的判断车辆故障并提供解决方案；</td>
		</tr>
		<tr>
			<th>2) 汽修案例：</th>
			<td>互联网最全面的汽车维修案例大全。图文并茂更直观；</td>
		</tr>
		<tr>
			<th>3) 常用资料：</th>
			<td>覆盖热门车型的最新汽车维修手册，包含正时匹配，电脑阵脚，保养归零等；</td>
		</tr>
		<tr>
			<th>4) 查故障码：</th>
			<td>搜索故障码，提供详细解释和解决方案；</td>
		</tr>
		<tr>
			<th>5) 汽&nbsp;修&nbsp;人：</th>
			<td>提供汽修问答交流和生活交友交流平台；</td>
		</tr>
		<tr>
			<th>6) 助&nbsp;&nbsp;&nbsp;&nbsp;人：</th>
			<td>对接车主，为车主提供服务，提升个人价值；</td>
		</tr>
	</table>
	
</div>
<div class="h-content">
</div>
<div class="container-fluid h-picture h-download" style="padding-bottom:0 !important">
	<div class="row">
		<div class="col-xs-6 col-sm-6" style="text-align:right">
			<a href="https://appsto.re/hk/pzy-9.i">
				<img src="<?php echo URL_MOBILE;?>/images/share_code_app/download_ios_big.png" 
					style="max-width:250px;width:100%" 
				/>
			</a>
		</div>
		<div class="col-xs-6 col-sm-6" style="text-align:left">
			<a href="http://www.haohaoxiuche.com/download/hhxcjsb.apk">
				<img src="<?php echo URL_MOBILE;?>/images/share_code_app/download_android_big.png" 
					style="max-width:250px;width:100%" 
				/>
			</a>
		</div>
	</div>
</div>
<h3 style="color:#db1800;text-align:center">遇汽修难题 找好好修车</h3>
</body>
</html>



