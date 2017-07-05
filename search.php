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

// include class.secure.php to protect this file and the whole CMS!
if (defined('LEPTON_PATH')) {
  if (defined('LEPTON_VERSION')) include(LEPTON_PATH.'/framework/class.secure.php');
} else {
  $oneback = "../";
  $root = $oneback;
  $level = 1;
  while (($level < 10) && (!file_exists($root.'/framework/class.secure.php'))) {
    $root .= $oneback;
    $level += 1;
  }
  if (file_exists($root.'/framework/class.secure.php')) {
    include($root.'/framework/class.secure.php');
  } else {
    trigger_error(sprintf("[ <b>%s</b> ] Can't include class.secure.php!",
    $_SERVER['SCRIPT_NAME']), E_USER_ERROR);
  }
}
// end include class.secure.php


/**
 * This function will be called by the search function and returns the results
 * via print_excerpt2() for the specified SECTION_ID
 *
 * @param array $search
 * @return boolean
 */
function topics_search($search) {
  global $database;

  // include module_settings
  include LEPTON_PATH.'/modules/'.basename(dirname(__FILE__)).'/defaults/module_settings.default.php';
  include LEPTON_PATH.'/modules/'.basename(dirname(__FILE__)).'/module_settings.php';

  $divider = ".";
  $result = false;

  $SQL = "SELECT * FROM `".TABLE_PREFIX."mod_topics` ".
    "WHERE `section_id`='{$search['section_id']}' AND `active` > '3' ORDER BY `topic_id` DESC";
  if (null == ($query = $database->query($SQL)))
    trigger_error(sprintf('[%s - %s] %s', __FUNCTION__, __LINE__, $database->get_error()), E_USER_ERROR);

  while (false !== ($topic = $query->fetchRow(MYSQL_ASSOC))) {
    $text = $topic['title'].$divider.$topic['description'].$divider.$topic['content_long'].$divider;
    $text = stripcslashes($text);
    $text = str_replace('||', '', $text);
    $item = array(
        'page_link' => $topics_search_directory.$topic['link'],
        'page_link_target' => "",
        'page_title' => $topic['title'],
        'page_description' => $topic['short_description'],
        'page_modified_when' => $topic['posted_modified'],
        'page_modified_by' => $topic['posted_by'],
        'text' => $text,
        'max_excerpt_num' => $search['default_max_excerpt']
    );
    if (print_excerpt2($item, $search)) $result = true;
  }
  return $result;
} // topics_search()

