<?php
include('init.php');

try {
	$dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", $dblogin, $dbpass);
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$sql = "";
} catch (Exception $e) {
    $dberr="<div class='err'><span class='icon-warning'> </span>SQL ERROR!</div>";
}

if ($nextupdate <= 0) {
	$url = "http://api.steampowered.com/ISteamWebAPIUtil/GetSupportedAPIList/v0001/?format=json&key=$steamkey";
	$ch = curl_init();
	$curlhead = array("Accept-Encoding: gzip,deflate","Connection: Keep-Alive");
	curl_setopt($ch, CURLOPT_ENCODING, "gzip");
	curl_setopt($ch, CURLOPT_HTTPHEADER, $curlhead);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_USERAGENT, "Steam Otter ($SOversion)");
	curl_setopt($ch, CURLOPT_URL, "$url");
	$json = curl_exec($ch);
	if (file_exists($tempfile)) {unlink($tempfile);}
	file_put_contents($tempfile,$json);
	curl_close($ch);
} else {
	$json = file_get_contents($tempfile);
}
$json = json_decode($json);

foreach ($json->apilist->interfaces as $inter) {
	$thestart="";
	$thetitle="";
	$theparams="";
	$theend="";
	$pr=0;
	$prk=0;
	$i++;
	$inter_name = $inter->name;
	$thestart .= "<h3 class='apititle'><strong> $inter_name</strong>";
	foreach ($inter->methods as $method) {
		$par++;
		$pr++;
		$p=0;
		$method_name = $method->name;
		$method_version = $method->version;
		$method_httpmethod = $method->httpmethod;
		$theparams .= "<br /><strong><span class='s_orange'>$method_name</span>:</strong> (<strong><span class='s_blue'>V$method_version</span></strong> by <strong><span class='s_blue'><i>$method_httpmethod</i></span></strong>) - $inter_name/$method_name/v000$method_version/<br />"; // METHODE NAME + VERSION + GET|POST
		foreach ($method->parameters as $param) {
			$param_name = $param->name;
			if ($param_name) {
				$p++;
				if ($p == 1) {
					$theparams .= "<div class='param1 tcenter'><strong>Parameter</strong></div><div class='param2 tcenter'><strong>Description</strong></div><div class='param3 tcenter'><strong>Type</strong></div><div class='param4 tcenter'><strong>Optional</strong></div><br />";
				}
			}
			$param_type = $param->type;
			$param_optional = $param->optional;
			if ($param_name == "key") {
				if ($param_optional != 1) {
					$prk++;
				}
			}
			if ($param_optional == 1) {
				$param_optional = "<span class='icon-checkmark-circle'></span>";
			} else {
				$param_optional = "<span class='icon-cancel-circle'></span>";
			}
			$param_description = $param->description;
			if ($param_description == "") {$param_description="&nbsp;";}
			$theparams .= "<div class='param1'>$param_name</div><div class='param2 s_dblue'><i>$param_description</i></div><div class='param3 tcenter'>$param_type</div><div class='param4 tcenter'>$param_optional</div><br />";
		}
	}
	if ($prk == 0) {
		$thekey = "<span class='icon-key s_green'></span> ($prk/$pr)";
	} elseif ($prk != $pr) {
		$thekey = "<span class='icon-key s_orange'></span> ($prk/$pr)";
	} else {
		$thekey = "<span class='icon-key s_red'></span> ($prk/$pr)";
	}
	$theapilist .= $thestart."<span class='gotoright'>".$thekey."</span>"."</h3><div>".$theparams."</div>";
}
?>
<!doctype html>
<html>
	<head>
		<title>Steam Otter</title>
		<link rel="stylesheet" type="text/css" href="style.css">
		<script type="text/javascript" src="http://code.jquery.com/jquery-git2.js"></script>
		<script type="text/javascript" src="http://code.jquery.com/ui/jquery-ui-git.js"></script>
		<script type="text/javascript" src="jquery.countdown.min.js"></script>
		<script type='text/javascript'>$(function(){$('#nextupdate').countdown({until:'<?= $nextupdate ?> s',layout:'{hnn}:{mnn}:{snn}'})});</script>
		<script>$(document).ready(function(){$('#accordion').accordion({active:false,autoHeight:true,collapsible:true,heightStyle:"content",icons:{activeHeader:"icon-minus",header:"icon-plus"}})});</script>
	</head>
	<body>
		<?= $dberr ?>
		<div class='iBlockL'>
			<h1><span class='icon-steam'> </span>Steam Otter:</h1>
			<h2>The Steam Web API Analyzer</h2>
			<h3><span class='icon-clock'> </span>Next update: <span id='nextupdate'></span><br /></h3>
			<h3><span class='icon-flag'> </span><?= $i ?> API found</h3>
			<h3><span class='icon-lab'> </span><?= $par ?> methods found</h3>
			<h3><span class='icon-feed'> </span>3 new updates</h3>
			<hr />
			<div class='bupdate bu_new'>
				<div>
					<span class='icon-download t20'></span>
				</div>
				<div>
					New: %param%<br />
					ISteamWebAPIUtil/GetSupportedAPIList/v0001/<br />
					0000/00/00 - 00:00:00
				</div>
			</div>
			<div class='bupdate bu_update'>
				<div>
					<span class='icon-loop t20'></span>
				</div>
				<div>
					Updated: %param%<br />
					ISteamWebAPIUtil/GetSupportedAPIList/v0001/<br />
					0000/00/00 - 00:00:00
				</div>
			</div>
			<div class='bupdate bu_delete'>
				<div>
					<span class='icon-upload t20'></span>
				</div>
				<div>
					Removed: %param%<br />
					ISteamWebAPIUtil/GetSupportedAPIList/v0001/<br />
					0000/00/00 - 00:00:00
				</div>
			</div>
			<div><br />&copy;2013, Steam Otter <?= $SOversion ?> by iveinsomnia | <a href='https://github.com/iveinsomnia/Steam-Otter'><span class='icon-github'> </span>GitHub</a> | <span class='icon-info'> </span>About</div>
			<ul>
				<li><a href='http://jquery.com/'>JQuery</a> Version: <span id='jqver'></span></li>
				<li><a href='http://jqueryui.com/'>JQueryUI</a> Version: <span id='jquiver'></span></li>
				<li>JQuery countdown plugin by <a href='http://keith-wood.name/countdown.html'>Keith Wood</a></li>
				<li><span class='icon-IcoMoon'> </span>Icons by <a href='http://icomoon.io/'>Icomoon.</a></li>
			</ul>
			<script>
				$("#jqver").text(jQuery.fn.jquery);
				$("#jquiver").text($.ui.version);
			</script>
		</div>

		<div class='iBlockR'>
			<h3>Actual commands:</h3>
			<div id="accordion">
				<?= $theapilist ?>
			</div>
		</div>
		<div class='clear'></div>
	</body>
</html>
