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

if (!defined('LEPTON_PATH'))
	require_once("../../config.php");

global $database;

// load the default settings
require_once(LEPTON_PATH.'/modules/'.basename(dirname(__FILE__)).'/defaults/module_settings.default.php');
require_once(LEPTON_PATH.'/modules/'.basename(dirname(__FILE__)).'/module_settings.php');

// the Section ID is not set yet
$section_id = 0;
$parameters = array();

// use the parameter 'section_id' as Section ID
if (isset($_GET['section_id']) && is_numeric($_GET['section_id'])) {
	$section_id = $_GET['section_id'];
	$parameters['section_id'] = $section_id;
}

// set the limit for delivering articles, default is 20
$article_limit = 20;
if (isset($_GET['limit'])) {
  $article_limit = (int) $_GET['limit'];
  $parameters['limit'] = $article_limit;
}

// use the parameter 'counter' to switch the counter on/off - default is true (on)
$use_counter = true;
if (isset($_GET['counter']) && (strtolower($_GET['counter']) == 'false')) {
  $use_counter = false;
  $parameters['counter'] = 'false';
}

if ($section_id == 0) {
  // read the first entry of the TOPICS settings to get a valid Section ID
  $SQL = "SELECT `section_id` FROM `".TABLE_PREFIX."mod_topics_settings` LIMIT 1";
  if (null == ($section_id = $database->get_one($SQL)))
    die(sprintf('[%s] %s', __LINE__, $database->get_error()));
  if ($section_id < 1)
    die(sprintf('[%s] %s', __LINE__, 'no section_id defined'));
}

// get the TOPICS settings for the Section ID
$SQL = sprintf("SELECT `sort_topics`,`section_title`,`section_description`,".
    "`use_timebased_publishing`,`page_id`,`picture_dir` FROM `%smod_topics_settings` WHERE ".
    "`section_id`='%d'", TABLE_PREFIX, $section_id);
if (null == ($query = $database->query($SQL)))
  die(sprintf('[%s] %s', __LINE__, $database->get_error()));

if ($query->numRows() == 1) {
	$settings = $query->fetchRow();
	$page_id = $settings['page_id'];
	$sort_topics = $settings['sort_topics'];
	$section_title = $settings['section_title'];
	$section_description = strip_tags($settings['section_description']);
	$use_timebased_publishing = $settings['use_timebased_publishing'];
	$picture_url = LEPTON_URL.$settings['picture_dir'].'/';
}
else {
  // settings not found
 	die (sprintf('[%s] %s', __LINE__, "no data found"));
}

$query_extra = '';
if ($use_timebased_publishing > 1) {
  $t = time();
  $query_extra = " AND (`published_when`='0' OR `published_when` <= '$t') AND ".
      "(`published_until`='0' OR `published_until` >= '$t')";
}

$SQL = sprintf("SELECT * FROM `%smod_topics` WHERE `section_id`='%s' AND `active`>'3'%s ".
    "ORDER BY `active` DESC, `published_when` DESC LIMIT %d",
    TABLE_PREFIX, $section_id, $query_extra, $article_limit);
if (null == ($query = $database->query($SQL)))
  die(sprintf('[%s] %s', __LINE__, $database->get_error()));

$topics = '';
$image_width = 100;
$image_width_px = $image_width.'px';
$last_publishing_date = 0;
// loop through the topics
while (false !== ($topic = $query->fetchRow())) {
  $topic_link = LEPTON_URL.$topics_virtual_directory.$topic['link'].PAGE_EXTENSION;
  $rfcdate = date('D, d M Y H:i:s O', (int) $topic["published_when"]);
  if ($last_publishing_date < (int) $topic['published_when'])
    $last_publishing_date = (int) $topic['published_when'];
  $title = stripslashes($topic["title"]);
  $content = stripslashes($topic["content_short"]);
  // we don't want any dbGlossary entries here...
  $content = str_replace('||', '', $content);
  if (file_exists(LEPTON_PATH .'/modules/droplets/droplets.php')) {
    // we must process the droplets to get the real output content
    include_once(LEPTON_PATH .'/modules/droplets/droplets.php');
    if (function_exists('evalDroplets'))
		evalDroplets($content);
  }
  if (!empty($topic['picture'])) {
    // add a image to the content
    $img_url = $picture_url.$topic['picture'];
$content = '
<div>
  <img style="float:left;width:'.$image_width_px.';height:auto;margin:0;padding:0 20px 20px 0;" src="'.$img_url.'" width="'.$image_width.'" alt="'.$title.'" />
  '.$content.'
</div>
';
  } // image
  // add the topic to the $topics placeholder
$topics .= '
    <item>
    	<title><![CDATA['.$title.']]></title>
    	<pubDate><![CDATA['.$rfcdate.']]></pubDate>
    	<description><![CDATA['.$content.']]></description>		
    	<guid>'.$topic_link.'</guid>
    	<link>'.$topic_link.'</link>
    </item>
';
} // while

$link = LEPTON_URL;
$language = strtolower(DEFAULT_LANGUAGE);
$category = WEBSITE_TITLE;
$charset = defined('DEFAULT_CHARSET') ? DEFAULT_CHARSET : 'utf-8';
$rfcdate = date('D, d M Y H:i:s O', $last_publishing_date);

if (count($parameters) > 0) {
	$atom_link = LEPTON_URL."/modules/topics/rss.php?".http_build_query($parameters);
}
else {
	$atom_link = LEPTON_URL."/modules/topics/rss.php";	
}

// create the XML body with the topics
$xml_body = '
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
  <channel>
    <title>'.$section_title.'</title>
    <link>'.$link.'</link>
    <description>'.$section_description.'</description>
    <language>'.$language.'</language>
    <category>'.$category.'</category>
    <generator>TOPICS for LEPTON CMS</generator>
    <pubDate><![CDATA['.$rfcdate.']]></pubDate>
    <ttl>60</ttl>
    <atom:link href="'.$atom_link.'" rel="self" type="application/rss+xml" />
    '.$topics.'
  </channel>
</rss>
';

if ($use_counter) {
  // ok - before we send out the feed we will track the caller!
  $date = date('Y-m-d');
  // exists entries for days in the past?
  $SQL = "SELECT DISTINCT `date`,`section_id` FROM `".TABLE_PREFIX."mod_topics_rss_count` WHERE `date`<'$date'";
  if (null == ($past = $database->query($SQL)))
    die(sprintf('[%s] %s', __LINE__, $database->get_error()));
  while (false !== ($old = $past->fetchRow())) {
    // walk through previous entries, add them to statistic and delete them from the count table
    $SQL = "SELECT COUNT(`md5_ip`) AS `callers`, SUM(`count`) AS `views` FROM `".TABLE_PREFIX."mod_topics_rss_count` WHERE `date`='{$old['date']}' AND `section_id`='{$old['section_id']}'";
    if (null == ($result = $database->query($SQL)))
      die(sprintf('[%s] %s', __LINE__, $database->get_error()));
    if (false ===($statistic = $result->fetchRow()))
      die(sprintf('[%s] %s', __LINE__, $database->get_error()));
    // insert the statistic into the statistic table
    $SQL = "INSERT INTO `".TABLE_PREFIX."mod_topics_rss_statistic` (`section_id`,`date`,`callers`,`views`) VALUES ('{$old['section_id']}','{$old['date']}','{$statistic['callers']}','{$statistic['views']}')";
    if (!$database->query($SQL))
      die(sprintf('[%s] %s', __LINE__, $database->get_error()));
    // now delete the old entries
    $SQL = "DELETE FROM `".TABLE_PREFIX."mod_topics_rss_count` WHERE `date`='{$old['date']}' AND `section_id`='{$old['section_id']}'";
    if (!$database->query($SQL))
      die(sprintf('[%s] %s', __LINE__, $database->get_error()));
  }
  // hash the remote IP address
  $md5_ip = md5($_SERVER['REMOTE_ADDR']);
  // check if this has called the feed previous
  $SQL = "SELECT * FROM `".TABLE_PREFIX."mod_topics_rss_count` WHERE `md5_ip`='$md5_ip' AND `section_id`='$section_id' AND `date`='$date'";
  if (null == ($query = $database->query($SQL)))
    die(sprintf('[%s] %s', __LINE__, $database->get_error()));
  if ($query->numRows() > 0) {
    // add this call to the existing record
    $update = $query->fetchRow();
    $count = $update['count']+1;
    $SQL = "UPDATE `".TABLE_PREFIX."mod_topics_rss_count` SET `count`='$count' WHERE `id`='{$update['id']}'";
    if (!$database->query($SQL))
      die(sprintf('[%s] %s', __LINE__, $database->get_error()));
  }
  else {
    // add a new record
    $SQL = "INSERT INTO `".TABLE_PREFIX."mod_topics_rss_count` (`section_id`, `md5_ip`, `count`, `date`) VALUES ('$section_id', '$md5_ip', '1', '$date')";
    if (!$database->query($SQL))
      die(sprintf('[%s] %s', __LINE__, $database->get_error()));
  }
} // $use_counter

// Sending XML header
header("Content-type: text/xml; charset=$charset" );

// Header info, Required by CSS 2.0
echo '<?xml version="1.0" encoding="'.$charset.'"?>';
// output XML content
echo $xml_body;
?>