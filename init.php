<?php
$now = time();
require('include.php');
$nextupdate = 24*60*60;
if (file_exists($tempfile)) {
	$filetime = filemtime($tempfile);
} else {
	$filetime = 0;
}
$nextupdate = $filetime+$nextupdate-$now;
$filetime = date("Y/m/d - H:i:s",$filetime);

$dberr="";
$i=0;
$par=0;
$inter = array();
$theapilist = "";
include('class.php');
?>