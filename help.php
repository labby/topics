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
 
// STEP 1:	Initialize
require('../../config.php');
if(!defined('LEPTON_PATH')) { exit("Cannot access this file directly"); }

require('permissioncheck.php');

// Load correct help fil
if(LANGUAGE_LOADED) {
    $help = LEPTON_PATH.'/modules/'.$mod_dir.'/languages/help-EN.php';
    if(file_exists(LEPTON_PATH.'/modules/'.$mod_dir.'/languages/help-'.LANGUAGE.'.php')) {
        $help = LEPTON_PATH.'/modules/'.$mod_dir.'/languages/help-'.LANGUAGE.'.php';
    }
}

// STEP 2:	Get actual settings from database
//$query_settings = $database->query("SELECT * FROM ".TABLE_PREFIX."mod_topics_settings WHERE section_id = '$section_id'");
//$settings = $query_settings->fetchRow();

// STEP 3:	Display the help page.
?>

<table cellpadding="2" cellspacing="0" border="0" align="center" width="100%">
	<tr>
		<td>
		<?php include($help); // Load help file ?>
			
		
			
		</td>
	</tr>
</table>
<table cellpadding="0" cellspacing="0" border="0" width="100%">
	<tr>
		<td>
			<input id="topics_cancel" type="button" value="<?php echo $TEXT['BACK']; ?>" onclick="javascript: window.location = '<?php echo ADMIN_URL; ?>/pages/modify.php?page_id=<?php echo $page_id.(isset($_GET['leptoken']) ? "&leptoken=".$_GET['leptoken'] : ""); ?>';" style="width: 100px; margin-top: 5px;" />
		</td>
	</tr>	
</table>

<?php
    $admin->print_footer();
?>