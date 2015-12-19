#!/usr/bin/php
<?php
define('HHXC',        TRUE);
define('API_ROOT',    dirname(dirname(dirname(__FILE__))));

require_once('config.development.php');
require_once('common.php');

KVStorageConnect(SSDB_HOST, SSDB_PORT, SSDB_PWD, SSDB_NAME);
var_dump(KVStorageSet("asd", array('id' => '12', 'key' => 12)));
var_dump(KVStorageExists("asd"));
var_dump(KVStorageScan('', 'asz'));
