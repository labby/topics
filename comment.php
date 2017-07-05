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

// Include config file
require('../../config.php');
if(!defined('LEPTON_PATH')) { exit("Cannot access this file directly"); }

$mod_dir = basename(dirname(__FILE__));
$tablename = $mod_dir;

// Check if there is a post id
if(!isset($_GET['id']) OR !is_numeric($_GET['id']) OR !isset($_GET['sid']) OR !is_numeric($_GET['sid'])) {
	header("Location: ".LEPTON_URL.'/modules/'.$mod_dir.'/nopage.php?err=1');
	exit(0);
}
$topic_id = (int) $_GET['id'];
$section_id = (int) $_GET['sid'];
if (($topic_id  * $section_id) == 0) {
	header("Location: ".LEPTON_URL.'/modules/'.$mod_dir.'/nopage.php?err=2');
	exit(0);
}


if (isset($_GET['nok'])) {$nok = $_GET['nok'];} else {$nok=0;}
if (isset($_COOKIE['comment'.$topic_id])) {
	$cArr = explode(',', $_COOKIE['comment'.$topic_id]);	
	$the_comment = (int) $cArr[0];
	$ct = time() - (int) $cArr[1];
	
	if ($ct > 300 OR $ct < 0) {$the_comment=0;} //schon lange abgelaufen
} else {
	$the_comment=0;
}

if ($nok <> 1) {	
	if ($the_comment > 0) {
		header("Location: ".LEPTON_URL.'/modules/'.$mod_dir.'/commentdone.php?cid='.$the_comment.'&tid='.$topic_id );
		exit(0);
	}
}

// Query post for page id
$res = $database->query("SELECT topic_id,title,section_id,page_id FROM ".TABLE_PREFIX."mod_".$tablename." WHERE topic_id = '$topic_id'");
if($res->numRows() == 0) {
	header("Location: ".LEPTON_URL.'/modules/'.$mod_dir.'/nopage.php?err=3');
	exit(0);
} else {
	$fetch_topic = $res->fetchRow();
	$page_id = $fetch_topic['page_id'];	
	$section_id = $fetch_topic['section_id'];
	//$topic_id = $fetch_topic['topic_id'];
	$topic_title = $fetch_topic['title'];
	define('PAGE_ID', $page_id);
	define('SECTION_ID', $section_id);
	define('TOPIC_ID', $topic_id);
	define('POST_TITLE', $topic_title);
	
	
	
		
	
	// don't allow commenting if its disabled, or if post or group is inactive
	//--------------------------------------------------------
	//ausgeschaltet:
	//Topics können auch kommentiert werden, wenn Seiten nicht aktiv sind.
	
	/*
	$t = time();
	$table_posts = TABLE_PREFIX."mod_topics";	
	$query = $database->query("
		SELECT p.topic_id
		FROM $table_posts AS p LEFT OUTER JOIN $table_groups AS g ON p.group_id = g.group_id
		WHERE p.topic_id='$topic_id' AND p.commenting != 'none' AND p.active = '1' AND ( g.active IS NULL OR g.active = '1' )
		AND (p.published_when = '0' OR p.published_when <= $t) AND (p.published_until = 0 OR p.published_until >= $t)
	");
	if($query->numRows() == 0) {
		header("Location: ".LEPTON_URL.PAGES_DIRECTORY."");
		exit(0);
	}
//--------------------------------------------------
*/
	// don't allow commenting if ASP enabled and user doesn't comes from the right view.php
	if(ENABLED_ASP && (!isset($_SESSION['comes_from_view']) OR $_SESSION['comes_from_view']!=TOPIC_ID)) {
		header("Location: ".LEPTON_URL.'/modules/'.$mod_dir.'/nopage.php?err=4');
		exit(0);
	}

	// Get page details
	$query_page = $database->query("SELECT parent,page_title,menu_title,keywords,description,visibility FROM ".TABLE_PREFIX."pages WHERE page_id = '$page_id'");
	if($query_page->numRows() == 0) {
		header("Location: ".LEPTON_URL.'/modules/'.$mod_dir.'/nopage.php?err=5');
		exit(0);
	} else {
		
		
		$page = $query_page->fetchRow();
		define('PARENT', $page['parent']);
		

		// Required page details
		define('PAGE_CONTENT', LEPTON_PATH.'/modules/'.$mod_dir.'/comment_page.php');
		// Include index (wrapper) file
		//require(LEPTON_PATH.'/index.php');
		
		//von Chio eingefügt
		require(LEPTON_PATH.'/modules/'.$mod_dir.'/commentframe.php');
		
		
	}
}

?>
