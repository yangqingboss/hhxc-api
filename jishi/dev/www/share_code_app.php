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
</head>
<body>
<div class="container-fluid">
	<div class="row">
		<div class="col-xs-6 col-md-4"></div>
		<div class="col-xs-12 col-sm-6 col-md-8"></div>
	</div>
</div>
</body>
</html>



