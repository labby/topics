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
require_once(LEPTON_PATH.'/modules/'.$mod_dir.'/defaults/module_settings.default.php');
require_once(LEPTON_PATH.'/modules/'.$mod_dir.'/module_settings.php');

//get and remove all php files created for the topics section
$query_details = $database->query("SELECT * FROM ".TABLE_PREFIX."mod_".$tablename." WHERE section_id = '$section_id' AND page_id = '$page_id'");
if($query_details->numRows() > 0) {
	while($row = $query_details->fetchRow()) {
		// Unlink topic access file anyway
		if(is_writable(LEPTON_PATH.$topics_directory.$row['link'].PAGE_EXTENSION)) {
			unlink(LEPTON_PATH.$topics_directory.$row['link'].PAGE_EXTENSION);
		}
		
		$t_id = $row['topic_id'];
		if ($row['hascontent'] < 2) {
			$database->query("DELETE FROM ".TABLE_PREFIX."mod_".$tablename." WHERE topic_id = '$t_id'");		
		}	
	}
} 

$hide_it = 0 - $section_id;
$theq = "UPDATE ".TABLE_PREFIX."mod_".$tablename." SET section_id = '$hide_it' WHERE section_id = '$section_id' AND page_id = '$page_id'";
$database->query($theq);
$theq = "UPDATE ".TABLE_PREFIX."mod_".$tablename."_settings SET section_id = '$hide_it' WHERE section_id = '$section_id' AND page_id = '$page_id'";
$database->query($theq);



//check to see if any other sections are part of the topics page, if only 1 topics is there delete it
$query_details = $database->query("SELECT * FROM ".TABLE_PREFIX."sections WHERE page_id = '$page_id'");
if($query_details->numRows() == 1) {
	$query_details2 = $database->query("SELECT * FROM ".TABLE_PREFIX."pages WHERE page_id = '$page_id'");
	$link = $query_details2->fetchRow();
	if(is_writable(LEPTON_PATH.PAGES_DIRECTORY.$link['link'].PAGE_EXTENSION)) {
		unlink(LEPTON_PATH.PAGES_DIRECTORY.$link['link'].PAGE_EXTENSION);
	}
}

?>