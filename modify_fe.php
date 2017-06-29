<?php 
require('../../config.php');
if(!defined('LEPTON_PATH')) { exit("Cannot access this file directly"); }

$mod_dir = basename(dirname(__FILE__));
$tablename = $mod_dir;
require(LEPTON_PATH.'/modules/'.$mod_dir .'/modify.php');

?>