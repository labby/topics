<?php

/**
 * 
 *  @module      	Topics
 *  @author         Chio Maisriml, Dietrich Roland Pehlke, erpe
 *  @license        http://creativecommons.org/licenses/by/3.0/
 *  @platform       see info.php of this addon
 *  @license terms  see info.php of this addon
 *  @version        see info.php of this module
 *  
 *
 */

//require('../../config.php');

if (!defined('LEPTON_PATH')) { die('Sopperlott!'); }

global $database;
global $admin;

$mod_dir = basename(dirname(__FILE__));
$tablename = $mod_dir;

$query = $database->query("SELECT * FROM ".TABLE_PREFIX."mod_".$mod_dir);
$fetch = $query->fetchRow();	
// Add field groups_id
if(!isset($fetch['groups_id'])){
	if($database->query("ALTER TABLE `".TABLE_PREFIX."mod_".$mod_dir."` ADD `groups_id` VARCHAR(255) NOT NULL DEFAULT ''")) {
		echo '<span class="good">Database Field groups_id added successfully</span><br />';
	}
		echo '<span class="bad">'.mysql_error().'</span><br />';
} else {
	echo '<span class="ok">Database Field groups_id exists, update not needed</span><br />';
}
	
$query = $database->query("SELECT * FROM ".TABLE_PREFIX."mod_".$mod_dir.'_comments');
$fetch = $query->fetchRow();	
// Add field commentextra
if(!isset($fetch['commentextra'])){
	if($database->query("ALTER TABLE `".TABLE_PREFIX."mod_".$mod_dir."_comments` ADD `commentextra` VARCHAR(255) NOT NULL DEFAULT ''")) {
		echo '<span class="good">Database Field commentextra added successfully</span><br />';
	}
		echo '<span class="bad">'.mysql_error().'</span><br />';
} else {
	echo '<span class="ok">Database Field commentextra exists, update not needed</span><br />';
}	
	

// create the RSS count table
$SQL = "CREATE TABLE IF NOT EXISTS `".TABLE_PREFIX."mod_topics_rss_count` ( ".
    "`id` INT(11) NOT NULL AUTO_INCREMENT, ".
    "`section_id` INT(11) NOT NULL DEFAULT '-1', ".
    "`md5_ip` VARCHAR(32) NOT NULL DEFAULT '', ".
    "`count` INT(11) NOT NULL DEFAULT '0', ".
    "`date` DATE NOT NULL DEFAULT '0000-00-00', ".
    "`timestamp` TIMESTAMP, ".
    "PRIMARY KEY (`id`), ".
    "KEY (`md5_ip`, `date`) ".
    ") ENGINE=MyIsam AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci";
if (!$database->query($SQL))
  $admin->print_error($database->get_error());

// create the RSS statistics table
$SQL = "CREATE TABLE IF NOT EXISTS `".TABLE_PREFIX."mod_topics_rss_statistic` ( ".
    "`id` INT(11) NOT NULL AUTO_INCREMENT, ".
    "`section_id` INT(11) NOT NULL DEFAULT '-1', ".
    "`date` DATE NOT NULL DEFAULT '0000-00-00', ".
    "`callers` INT(11) NOT NULL DEFAULT '0', ".
    "`views` INT(11) NOT NULL DEFAULT '0', ".
    "`timestamp` TIMESTAMP, ".
    "PRIMARY KEY (`id`), ".
    "KEY (`date`) ".
    ") ENGINE=MyIsam AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci";
if (!$database->query($SQL))
  $admin->print_error($database->get_error());

$mod_dir = basename(dirname(__FILE__));
$tablename = $mod_dir;
require_once(LEPTON_PATH.'/modules/'.$mod_dir.'/inc/upgrade.inc.php');

/**
 *	import droplets
 *
 */ 
if (!function_exists('droplet_install')) {
    include_once LEPTON_PATH.'/modules/droplets/functions.php';
}

if (file_exists(LEPTON_PATH.'/modules/topics/droplets/droplet_topics_rss_statistic.zip')) {
droplet_install(LEPTON_PATH.'/modules/topics/droplets/droplet_topics_rss_statistic.zip', LEPTON_PATH . '/temp/unzip/');
}


?>