#!/usr/bin/php
<?php
define('HHXC',        TRUE);
define('API_ROOT',    dirname(dirname(dirname(__FILE__))));

require_once('config.development.php');
require_once('common.php');

var_dump(JPushUser('Hello, world', '18319388532'));
