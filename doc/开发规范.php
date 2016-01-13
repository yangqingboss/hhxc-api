<?php header('Content-Type: text/html; charset=utf-8'); ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="utf-8" />
<title>API开发规范</title>
<link rel="stylesheet" href="http://apps.bdimg.com/libs/bootstrap/3.3.4/css/bootstrap.min.css" />
</head>
<body>
<h1 style="text-align:center">好好修车API开发规范</h1>
<h2>一、代码架构</h2>
<p>代码目录如下：http://www.haohaoxiuche.com/hhxc-api/</p>
<ol>
	<li>common.php API公共函数模块，包括MySQL数据库封装、SSDB日志数据库、短信接口、消息推送和其他常用函数等</li>
	<li>config.development.php 开发模式的配置文件，支持调试模式和错误消息</li>
	<li>config.test.php 测试模式的配置文件，支持调试模式和错误消息，主要用于生产机的测试数据库</li>
	<li>config.production.php 生产模式的配置文件，屏蔽调试模式和错误消息，用于最终代码发布</li>
	<li>config.mobile.php 针对移动版Web网站的配置文件，主要用于/mobile目录下的代码</li>
	<li>/lib 第三方引用的PHP库</li>
	<li>/mobile 移动版Web网站，还包括新版API所引用的CSS和图片等</li>
	<li>/jishi 技师版API代码
		<ol>
			<li>dev 开发模式的API代码
				<ol>
					<li>index.php 初始化配置文件和数据库 技师版公共函数</li>
					<li>APIxxx.php 第xxx个API代码实现</li>
					<li>test.php 测试脚本，暂只支持Unix/Linux系统</li>
					<li>/samples 测试数据
						<ol>
							<li>xxx.php 和APIxxx.php相对应的测试数据脚本</li>
						</ol>
					</li>
				</ol>
			</li>
			<li>1.9.0 正式发布的1.9.0版本API</li>
			<li>1.9.5 等等</li>
		</ol>
	</li>
</ol>
</body>
</html>
