<?php 
require_once(dirname(__FILE__).'/../../../config.php');
if(!defined('WB_PATH')) { 	die("sorry, no access..");}

// Get id
if(isset($_GET['section_id']) AND is_numeric($_GET['section_id']) AND isset($_GET['page_id']) AND is_numeric($_GET['page_id'])) {
	$section_id = (int) $_GET['section_id']; 
	$page_id = (int) $_GET['page_id']; 
} else {
   die('no section given');
}

include('getbasics.inc.php');

?>
Upload:
<form name="upload" action="uploadview.php" method="post" style="margin: 0; width:90%;"  enctype="multipart/form-data">
	<input type="hidden" name="section_id" value="<?php echo $section_id; ?>">
	<input type="hidden" name="page_id" value="<?php echo $page_id; ?>">
	
	
<input type="file" class="inputfield" name="uploadpic" style="width:350px; "/>
<p><input type="checkbox" name="nooverwrite" value="1"> Do NOT overwrite existing files</p>
<p>&nbsp;</p>	
<p>Current Resize Settings (width/height):<br />
<?php echo 'Zoom: '.$w_zoom.'/'.$h_zoom.', View: '.$w_view.'/'.$h_view.', Thumbs: '.$w_thumb.'/'.$h_thumb.'<br/>'; 
?>
Resize all: <input type="checkbox" name="resize[]" value="thumb"> Thumbs | <input type="checkbox" name="resize[]" value="view"> View | <input type="checkbox" name="resize[]" value="zoom"> Zoom 
</p>	

<!--table>
<tr><td>&nbsp;</td><td>width:</td><td>height:</td></tr>
<tr><td>Zoom:</td><td><input type="text" name="w_zoom" size="4" value="900" style="width:40px; "></td><td><input type="text" name="h_zoom" size="4" value="600" style="width:40px; "></td></tr>
<tr><td>View:</td><td><input type="text" name="w_view" size="4" value="200" style="width:40px; "></td><td><input type="text" name="h_view" size="4" value="160" style="width:40px; "></td></tr>
<tr><td>Thumb:</td><td><input type="text" name="w_thumb" size="4" value="100" style="width:40px; "></td><td><input type="text" name="h_thumb" size="4" value="100" style="width:40px; "></td></tr>

</table-->
	
	



<p style="text-align:right;"><input type="submit"  value="SUBMIT"></p>
</form>