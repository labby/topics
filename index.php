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
 
// Check if there is a specific topic defined
/*if (isset($_GET['topic_id']) AND is_numeric($_GET['topic_id']) AND $_GET['topic_id'] >= 0) {
	$topic_id = $_GET['topic_id'];
	define('TOPIC_ID',$topic_id); 	
}*/

if (isset($_GET['topic'])) {
	$topic_link = $_GET['topic'];	
} else {
	header('Location: ../../');
}

$mod_dir = basename(dirname(__FILE__));
$tablename = $mod_dir;

// Include config file
require_once('../../config.php');
// Check if the config file has been set-up
if(!defined('LEPTON_PATH')) {
	header('Location: ../../');	
	exit(0);
}


require_once(LEPTON_PATH.'/framework/class.frontend.php');
require_once(LEPTON_PATH.'/framework/summary.functions.php');
// Create new frontend object
$wb = new frontend();
$topic_link2 = save_filename($topic_link);
if ($topic_link2 !== $topic_link) {
	header('Location: ../../');	
	exit(0);
}

$theq = "SELECT topic_id, section_id, page_id FROM ".TABLE_PREFIX."mod_".$tablename." WHERE link = '".$topic_link."'";
$query_topics = $database->query($theq);
$num_topics = $query_topics->numRows();
if ($num_topics == 1) {
	$topic = $query_topics->fetchRow();	
	$topic_id = $topic['topic_id'];
	$section_id = $topic['section_id'];
	$page_id = $topic['page_id'];
	define('TOPIC_ID',$topic_id);	
	require(LEPTON_PATH."/index.php");
} else {
	header("HTTP/1.0 404 Not Found");

	//die("he!");
}
?>