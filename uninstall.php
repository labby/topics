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

// Must include code to stop this file being access directly
if(defined('LEPTON_PATH') == false) { exit("Cannot access this file directly"); }

$mod_dir = basename(dirname(__FILE__));
$tablename = $mod_dir;
// include module_settings
include(LEPTON_PATH.'/modules/'.$mod_dir.'/defaults/module_settings.default.php');
if (file_exists(LEPTON_PATH.'/modules/'.$mod_dir.'/module_settings.php')) { 
include(LEPTON_PATH.'/modules/'.$mod_dir.'/module_settings.php'); 
}

$database->query("DROP TABLE IF EXISTS `".TABLE_PREFIX."xsik_".$tablename."`");
$database->query("DROP TABLE IF EXISTS `".TABLE_PREFIX."xsik_".$tablename."_comments`");
$database->query("DROP TABLE IF EXISTS `".TABLE_PREFIX."xsik_".$tablename."_settings`");
$database->query("DROP TABLE IF EXISTS `".TABLE_PREFIX."xsik_".$tablename."_rss_count`");
$database->query("DROP TABLE IF EXISTS `".TABLE_PREFIX."xsik_".$tablename."_statistic`");

$database->query("RENAME TABLE `".TABLE_PREFIX."mod_".$tablename."` TO `".TABLE_PREFIX."xsik_".$tablename."`");
$database->query("RENAME TABLE `".TABLE_PREFIX."mod_".$tablename."_comments` TO `".TABLE_PREFIX."xsik_".$tablename."_comments`");
$database->query("RENAME TABLE `".TABLE_PREFIX."mod_".$tablename."_settings` TO `".TABLE_PREFIX."xsik_".$tablename."_settings`");
$database->query("RENAME TABLE `".TABLE_PREFIX."mod_".$tablename."_rss_count` TO `".TABLE_PREFIX."xsik_".$tablename."_rss_count`");
$database->query("RENAME TABLE `".TABLE_PREFIX."mod_".$tablename."_rss_statistic` TO `".TABLE_PREFIX."xsik_".$tablename."_rss_statistic`");

require_once(LEPTON_PATH.'/framework/summary.functions.php');
rm_full_dir(LEPTON_PATH.$topics_directory);



?>