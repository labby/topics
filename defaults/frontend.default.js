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
 
function addcomment (cid, t) {
	if (cid != lastcommentid) {
		document.getElementById("lastcomment").innerHTML  = t;
	} 
} 

function showcommenturl (t2, t1) {
	theurl = t1 + t2;
	window.open(theurl); 
}

function resizeframe (h) {
	h = 10 + h;
	hpx = ''+h+'px';
	document.getElementById("extrasager").style.height = hpx;
}

function makesmaller () {
	document.getElementById("extrasager").style.height = "30px";
}


