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

$starttime = array_sum(explode(" ",microtime()));

// Include config file
//require_once('../../config.php');
if(!defined('LEPTON_PATH')) { exit("Cannot access this file directly"); }


require_once(LEPTON_PATH.'/framework/class.frontend.php');
// Create new frontend object
$wb = new frontend();

//--------------------------------------------------------
// Brauchen wir hier was davon?


/*
// Figure out which page to display
// Stop processing if intro page was shown
$wb->page_select() or die();

// Collect info about the currently viewed page
// and check permissions
$wb->get_page_details();

// Collect general website settings
$wb->get_website_settings();
--------------------------------------------------------------*/
// Load functions available to templates, modules and code sections
// also, set some aliases for backward compatibility
require(LEPTON_PATH.'/framework/summary.frontend_functions.php');
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head><title>iframe</title>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo defined('DEFAULT_CHARSET') ? DEFAULT_CHARSET :'utf-8';?>" />
<meta name="robots" content="noindex,nofollow" />
<link href="comment_frame.css" rel="stylesheet" type="text/css"/>	
</head>
<body>
<table id="wraptable"><tr><td>
<?php page_content();?>
</td><td>

</td></tr></table>
</body></html>



