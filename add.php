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

if(!defined('LEPTON_PATH')) { exit("Cannot access this file directly"); }

// obtain module directory
$mod_dir = basename(dirname(__FILE__));
$tablename = $mod_dir;

$mpath = LEPTON_PATH.'/modules/'.$mod_dir.'/';
if (!file_exists($mpath.'module_settings.php')) { copy($mpath.'defaults/module_settings.default.php', $mpath.'module_settings.php') ; }
if (!file_exists($mpath.'frontend.css')) { copy($mpath.'defaults/frontend.default.css', $mpath.'frontend.css') ; }
if (!file_exists($mpath.'comment_frame.css')) { copy($mpath.'defaults/comment_frame.default.css', $mpath.'comment_frame.css') ; }
if (!file_exists($mpath.'frontend.js')) { copy($mpath.'defaults/frontend.default.js', $mpath.'frontend.js') ; }

require_once($mpath.'defaults/add_settings.default.php');
require_once($mpath.'defaults/module_settings.default.php');
require_once($mpath.'module_settings.php');
require_once($mpath.'functions_small.php');

$fetch_menu = array();
$database->execute_query(
	"SELECT `menu_title`, `link` FROM `".TABLE_PREFIX."pages` WHERE `page_id` = ".$page_id,
	true,
	$fetch_menu,
	false
);
$section_title = $fetch_menu['menu_title'];

// get first topics_settings inside DB
$temp_topics_settings = array();
$database->execute_query(
	"SELECT * FROM `".TABLE_PREFIX."mod_".$mod_dir."_settings` WHERE `is_master_for` = '' ORDER BY `section_id` DESC LIMIT 1",
	true,
	$temp_topics_settings,
	false
);

if( count($temp_topics_settings) > 0 ) {

	$temp_topics_settings['section_id']	= $section_id;
	$temp_topics_settings['page_id'] = $page_id;
	$temp_topics_settings['section_title'] = $section_title;
	
} else {
	
	//	none found ... we are using the vars from 'modules_settings.php'. 	
	$temp_topics_settings = array(
		"section_id"	=> $section_id,
		"page_id"		=> $page_id,
		"section_title"	=> $section_title,
		"section_description"	=> $section_description,
		"sort_topics"	=> $sort_topics,
		"use_timebased_publishing"	=> $use_timebased_publishing,
		"picture_dir"	=> $picture_dir,
		"header"	=> $header,
		"topics_loop"	=> $topics_loop,
		"footer"	=> $footer,
		"topics_per_page" => $topics_per_page,
		"topic_header"	=> $topic_header,
		"topic_footer"	=> $topic_footer,
		"topic_block2"	=> $topic_block2,
		"pnsa_string"	=> $pnsa_string,
		"pnsa_max"		=> $pnsa_max,
		"comments_header"	=> $comments_header,
		"comments_loop"		=> $comments_loop,
		"comments_footer"	=> $comments_footer,
		"commenting"		=> $commenting,
		"default_link"		=> $default_link,
		"use_captcha"		=> $use_captcha,
		"sort_comments"		=> $sort_comments
	);
	
	include('defaults/first-topics.php');
}

$database->build_and_execute(
	'insert',
	TABLE_PREFIX."mod_".$mod_dir."_settings",
	$temp_topics_settings
);

if(true === $database->is_error()) die( $database->get_error() );

// Add a frirst topic_
// include('defaults/first-topics.php');
if (isset($firsttopic)) {
	$database->query($firsttopic);	
	// Get the id
	$topic_id = $database->get_one("SELECT LAST_INSERT_ID()");
	
	$filename = LEPTON_PATH.$topics_directory.'welcome'.PAGE_EXTENSION;
	define('TOPICS_DIRECTORY_DEPTH', $topics_directory_depth);
	topics_archive_file ($filename, $topic_id, $section_id, $page_id);
}

?>