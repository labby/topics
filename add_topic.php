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

require('../../config.php');
if(!defined('LEPTON_PATH')) { exit("Cannot access this file directly"); }

require('permissioncheck.php');

require_once(__DIR__.'/defaults/module_settings.default.php');
require_once(__DIR__.'/module_settings.php');

// Include the ordering class
require(LEPTON_PATH.'/framework/class.order.php');
// Get new order
$order = new order(TABLE_PREFIX.'mod_'.$tablename, 'position', 'topic_id', 'section_id');
$position = $order->get_new($section_id);

// Get default commenting
$commenting = $database->get_one("SELECT `commenting` FROM `".TABLE_PREFIX."mod_".$tablename."_settings` WHERE `section_id` = ".$section_id);

// Insert new row into database
$t = 0; //= topic is just startet, begin time is when first saved    //topics_localtime();
$theuser = $admin->get_user_id();

$fields = array(
	"section_id"	=> $section_id,
	"page_id"		=> $page_id,
	"position"		=> $position,
	"commenting"	=> $commenting,
	"active"		=> $activedefault,	// aldus: $activedefault comes from 'module_settings'
	"posted_by"		=> $theuser,
	"modified_by"	=> $theuser,
	"link"			=> "",
	"content_short"	=> "",
	"content_long"	=> "",
	"content_extra"	=> "",
	"tagcloud"		=> "", // field has no default
	"rating_base"	=> "", // field has no default
	"pnsa_cache"	=> "", // field has no default
	"authors"		=> ','.$theuser.',', // aldus: why as a list-item? The ","?
	"posted_first"	=> $t
);

$database->build_and_execute(
	"insert",
	TABLE_PREFIX."mod_".$tablename,
	$fields
);

if($database->is_error())
{
	$topic_id = NULL;
	
	$admin->print_error(
		LEPTON_tools::display( $database->get_error()),
		LEPTON_URL.'/modules/'.$mod_dir.'/modify_topic.php?page_id='.$page_id.'&section_id='.$section_id.'&topic_id='.$topic_id.'&fredit='.$fredit
	);
	
} else {
	// Get the last insert id
	$topic_id = $database->get_one("SELECT LAST_INSERT_ID();");

	$admin->print_success(
		$TEXT['SUCCESS'],
		LEPTON_URL.'/modules/'.$mod_dir.'/modify_topic.php?page_id='.$page_id.'&section_id='.$section_id.'&topic_id='.$topic_id.'&fredit='.$fredit
	);

}

// Print admin footer
if ($fredit == 1) {
	topics_frontendfooter();
} else {
	$admin->print_footer();
}

?>