<?php

if (!function_exists('getimagesize')) { die("<h2>function 'getimagesize' doesnt exist</h2>"); }

$theauto_header = false;
require_once(LEPTON_PATH.'/framework/class.admin.php');
$admin = new admin('Pages', 'pages_modify', $theauto_header, TRUE);
if(!$admin->is_authenticated()) { die(); }

//Das muss hier so gemacht werden:
require_once('../info.php');
$mod_dir = $module_directory;
$tablename = $module_directory;

// Get Settings
$query_settings = $database->query("SELECT * FROM ".TABLE_PREFIX."mod_".$tablename."_settings WHERE section_id = '$section_id' AND page_id = '$page_id'");
if($query_settings->numRows() != 1) { die('Wahh!'); }
$settings_fetch = $query_settings->fetchRow();
$picture_dir = $settings_fetch['picture_dir'];
$picture_dirurl = LEPTON_URL.$settings_fetch['picture_dir'];

$vv = explode(',',$settings_fetch['picture_values'].',-2,-2,-2,-2,-2,-2,-2,-2,-2,-2,-2,-2,-2,-2');
$w_zoom = (int) $vv[0]; if ($w_zoom == -2) {$w_zoom = 1000;}
$h_zoom = (int) $vv[1]; if ($h_zoom == -2) {$h_zoom = 0;}
$w_view = (int) $vv[2]; if ($w_view == -2) {$w_view = 200;}
$h_view = (int) $vv[3]; if ($h_view == -2) {$h_view = 0;}
$w_thumb = (int) $vv[4]; if ($w_thumb == -2) {$w_thumb = 100;}
$h_thumb = (int) $vv[5]; if ($h_thumb == -2) {$h_thumb = 100;}


?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>Topics Modify Pics</title>

<?php echo'
<script type="text/javascript">
var LEPTON_URL = "'.LEPTON_URL.'";
var MOD_URL = "'.LEPTON_URL.'/modules/'.$mod_dir.'";
</script>
<link rel="stylesheet" type="text/css" href="'.LEPTON_URL.'/modules/'.$mod_dir.'/picupload/picupload.css"  />
<script src="'.LEPTON_URL.'/include/jquery/jquery-min.js" type="text/javascript"></script>
<script src="'.LEPTON_URL.'/include/jquery/jquery-insert.js" type="text/javascript"></script>
<script src="'.LEPTON_URL.'/include/jquery/jquery-include.js" type="text/javascript"></script>
<link rel="stylesheet" type="text/css" href="'.LEPTON_URL.'/modules/'.$mod_dir.'/picupload/jcrob/css/jquery.Jcrop.css" />
';
?>
</head><body>