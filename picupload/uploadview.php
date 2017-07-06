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
 
require_once(dirname(__FILE__).'/../../../config.php');
if(!defined('LEPTON_PATH')) { 	die("sorry, no access..");}

// Get id
if(isset($_REQUEST['section_id']) AND is_numeric($_REQUEST['section_id']) AND isset($_REQUEST['page_id']) AND is_numeric($_REQUEST['page_id'])) {
	$section_id = (int) $_REQUEST['section_id']; 
	$page_id = (int) $_REQUEST['page_id']; 
} else {
   die('no section given');
}


include('getbasics.inc.php');
$picture_dir = LEPTON_PATH.$picture_dir;

if (($w_view == 0 AND $h_view == 0) OR ($w_thumb == 0 AND $h_thumb == 0)) {
	echo '<h2>no dimensions given!</h2><p><a href="javascript:window.history.back()">BACK</a></p>';
	die();
}

$leptoken = get_leptoken();

require_once(LEPTON_PATH."/framework/summary.functions.php");
require_once("imagefunctions.php");



//Actions?:
if(isset($_REQUEST['resize']) ) {
	$resize = $_REQUEST['resize'];
	
	//get all source pictures:
	$pic_arr = array();
	$file_dir = $picture_dir; //in view nachsehen und dann erst die zoom-bilder suchen
	$pic_dir=opendir($file_dir);
	while ($file=readdir($pic_dir)) {
		if ($file != "." && $file != "..") { $pic_arr[] = $file; }		
	}
	$found = count($pic_arr);
	$counter = 0;
	echo "resizing...";
	foreach ($pic_arr as $imagename ) {
		//echo $file.'<br>';
		$origpath = '';			
		$testpath = $picture_dir.'/zoom/orig-'.$imagename;
		
		
		if (file_exists($testpath)) { 
			$origpath = $testpath;				
			if (in_array("zoom", $resize) AND ( $w_zoom > 0 OR $h_zoom > 0) ) {
				$newfilepath = $picture_dir.'/zoom/'.$imagename; 
				$counter += resizepic($origpath, $newfilepath, $w_thumb, $h_thumb, 1);
			}
		} else {
			//no orig-
			$testpath = $picture_dir.'/zoom/'.$imagename; 
			if (file_exists($testpath)) { $origpath = $testpath; }
		}
		
		if ($origpath != '') {
			if (in_array("view", $resize)) {
				$newfilepath = $picture_dir.'/'.$imagename;
				$counter += resizepic($origpath, $newfilepath, $w_view, $h_view, 1);
			}
			if (in_array("thumb", $resize)) {
				$newfilepath = $picture_dir.'/thumbs/'.$imagename;
				$counter += resizepic($origpath, $newfilepath, $w_thumb, $h_thumb, 1);
			}			
		}	
	} // end for each
	
	echo '<br/>'.$counter. ' out of '.$found.' pictures resized';
} 

//--------------------------------------------------------------------------
//deleting not used pictures
//not finished.
/*
if ($resize == 'delnonused') {
	$query_pics = $database->query("SELECT picture FROM ".TABLE_PREFIX."mod_".$tablename." WHERE section_id = '$section_id' AND page_id = '$page_id' AND picture <> ''");
	$found = $query_pics->numRows();
	if($found == 0) { echo 'No pictures found'; return 0;}
	//to be continued
}
*/	
	

if(!isset($_REQUEST['fn']) ) {

	if (!isset($_FILES['uploadpic']['tmp_name']) OR $_FILES['uploadpic']['tmp_name'] == '' ) {
		echo '<h1>No picture loaded</h1>
		<a href="uploader.php?page_id='.$page_id.'&section_id='.$section_id.'">Back</a>';
		die();
	}
	list($width, $height, $type, $attr) = getimagesize($_FILES['uploadpic']['tmp_name']);
	
	$bildtype = "";
	if ($type == 1) {$bildtype = ".gif";}
	if ($type == 2) {$bildtype = ".jpg";}			
	if ($type == 3) {$bildtype = ".png";}
		
	
	if ($bildtype == "" OR $width == 0) {
		echo '<p style="color: red; text-align: center;">no picture file uploaded (jpg, gif, png)</p>';
		return 0;
	}
	
	//Verzeichnis erstellen, falls noch nicht vorhanden
	$newfileFolder = $picture_dir.'/zoom';
	if(!is_dir($newfileFolder)){
		$u = umask(0);
		if(!@mkdir($newfileFolder, 0777)){
			echo '<p style="color: red; text-align: center;">Could not create Zoom-folder</p>';
		}
		umask($u);
	}
	
	
	$fname = $_FILES['uploadpic']['name']; 
	$fname = substr($fname, 0, strlen($fname) - 4);
	$fname = save_filename($fname); 
	
	if(isset($_REQUEST['nooverwrite']) ) {
		$ncount = 1;
		while ($ncount < 100) {
			if ($ncount == 1) {
				$imagename = $fname.$bildtype;
			} else {
				$imagename = $fname.'-'.$ncount.$bildtype;
			}
			$imagepfad = $picture_dir.'/'.$imagename;
			if (!file_exists($imagepfad)) {break;}
			$ncount++;
		}
	} else {
		$imagename = $fname.$bildtype;
		$imagepfad = $picture_dir.'/'.$imagename;
	}
	
	
	$orig_ratio = $width / $height;
	
	if ($w_zoom == 0 AND $h_zoom == 0) {$w_zoom = $width; $h_zoom = $height;}
	
	if ($width > $w_zoom AND $height > $h_zoom) {
		// Original behalten und dann verkleinern
		$filepath = $picture_dir.'/zoom/orig-'.$imagename;
		if (! move_uploaded_file($_FILES['uploadpic']['tmp_name'], $filepath))  { die (' <h2>Speichern fehlgeschlagen!</h2>'); }
		$newfilepath = $picture_dir.'/zoom/'.$imagename;
		resizepic($filepath, $newfilepath, $w_zoom, $h_zoom);
		$filepath = $newfilepath;	
	} else {
		//nur verschieben
		$filepath = $picture_dir.'/zoom/'.$imagename;
		if (! move_uploaded_file($_FILES['uploadpic']['tmp_name'], $filepath))  { die (' <h2>Speichern fehlgeschlagen!</h2>'); }
	}
	
	
	
	$newfilepath = $picture_dir.'/'.$imagename;
	resizepic($filepath, $newfilepath, $w_view, $h_view);
	
	$newfilepath = $picture_dir.'/thumbs/'.$imagename;
	resizepic($filepath, $newfilepath, $w_thumb, $h_thumb);
} else {
//Filename given: Only show:
	$imagename = $_REQUEST['fn'];
	$imagename = str_replace('zoom/','',$imagename );
	$imagename = str_replace('thumbs/','',$imagename );
}



//Show:
?>
<script type="text/javascript">
function finishit() {
	parent.choosethispicture('<?php echo $imagename; ?>');
	parent.choosethispicture(0);
}
</script>

<?php
echo '<table class="modifyheader"><tr>
';
$query_pic= $database->query("SELECT * FROM ".TABLE_PREFIX."mod_".$tablename." WHERE picture = '$imagename'");
	if($query_pic->numRows() == 0) { 	
	echo '<td class="deletepic"><a href="modify_thumb.php?page_id='.$page_id.'&amp;section_id='.$section_id.'&amp;fn='.$imagename.'&amp;what=delete"><img src="../img/delete.png" alt="delete" title="delete" /></a></td>';
}

echo '<td><h2><a href="javascript:finishit()">'.$imagename.' <img src="../img/apply.png" alt="apply" title="apply" /></a></h2>
</td></tr></table>';





$tl='?t='.time();
$newfilepath = $picture_dir.'/thumbs/'.$imagename;
if (file_exists($newfilepath)) {
	echo '<table class="showpics"><tr><td class="showpicspic">Thumb:<br/><img class="uploadviewpic" src="'.$picture_dirurl.'/thumbs/'.$imagename.$tl.'" alt="thumb" /></td><td><br/>
	<a href="modify_thumb.php?page_id='.$page_id.'&amp;section_id='.$section_id.'&amp;fn='.$imagename.'&amp;what=thumb&leptoken='.$leptoken.'"><img src="../img/crop.gif" alt="crop" title="crop" /></a>
 	</td></tr></table>';
} else {
	echo '<a href="modify_thumb.php?page_id='.$page_id.'&amp;section_id='.$section_id.'&amp;fn='.$imagename.'&amp;what=thumb&leptoken='.$leptoken.'"><img src="../img/crop.gif" alt="crop" title="crop" /></a><p>/thumbs/'.$imagename.':<br /> not found</p>';
}

$newfilepath = $picture_dir.'/'.$imagename;
if (file_exists($newfilepath)) {
	echo '<table class="showpics"><tr><td class="showpicspic">View:<br/><img class="uploadviewpic" src="'.$picture_dirurl.'/'.$imagename.$tl.'" alt="view" /></td><td><br/>
	<a href="modify_thumb.php?page_id='.$page_id.'&amp;section_id='.$section_id.'&amp;fn='.$imagename.'&amp;what=view&leptoken='.$leptoken.'"><img src="../img/crop.gif" alt="crop" title="crop" /></a>
 	</td></tr></table>';
} else {
	echo '<a href="modify_thumb.php?page_id='.$page_id.'&amp;section_id='.$section_id.'&amp;fn='.$imagename.'&amp;what=view&leptoken='.$leptoken.'"><img src="../img/crop.gif" alt="crop" title="crop" /></a>
	<p>/'.$imagename.':<br /> not found</p>';
}

$newfilepath = $picture_dir.'/zoom/'.$imagename;
if (file_exists($newfilepath)) {
	echo '<p style="clear:both;">Zoom:<br /><img src="'.$picture_dirurl.'/zoom/'.$imagename.$tl.'" alt="zoom" /></p>';
} else {
	echo '<p>'.$newfilepath.'<br />Not found</p>';
}


	
?>


</body></html>