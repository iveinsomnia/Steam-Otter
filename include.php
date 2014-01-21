<?php
	if (!isset($_GET['steamkey'])) {
		$steamkey = ""; // Your Steam Web API key /!\ VERRY IMPORTANT !!
	} else {
		$steamkey = $_GET['steamkey']; 
	}
	$tempfile="temp/otter.json"; // cache JSon file
?>