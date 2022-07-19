<?php
/*
file uploader v1.1 |  2022-07-19
by @aaviator42
*/

//Folder in which to store uploaded files, include trailing slash
const UPL_UPLOAD_DIR = "files/";

//Impose file size limit?
const UPL_FILE_SIZE_LIMIT = true;
const UPL_MAX_FILE_SIZE = 1024*1000*100; //1024*x = x kilobytes

//Impose file extension allowances?
//If enabled, only files with these extensions can be uploaded
const UPL_FILE_ALLOWANCES = false;
const UPL_VALID_FORMATS = array("jpeg", "txt", "jpg", "pdf", "png", "gif", "bmp");

//Impose file extension exclusions?
//If enabled, files with these extensions can NOT be uploaded
const UPL_FILE_EXCLUSIONS = true;
const UPL_INVALID_FORMATS = array("php", "phar", "phtml", "sh", "exe", "js");

//CONFIG ENDS HERE	
//---------------------

iniSettings();

printHeader();
if(count($_FILES) > 0){
	processFileUpload();
}
printUploadForm();
printFooter();
		
exit(0);


function printHeader(){
	echo <<<ENDEND
	
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8" />
	<title>file uploader</title>
	<style>
	body {
		font-family: Verdana, sans-serif !important;
		padding: 2rem;
		max-width: 50rem;
		margin: auto;
		font-size: 1rem !important;
	}
	table {
		width: 100%;
		border: 0.01rem solid;
		margin-left: auto;
		margin-right: auto;
		border-collapse: collapse;
		display: block;
		overflow-x: auto;
		white-space: nowrap;
	}
	table tbody {
		display: table;
		width: 99.9999%;
	}
	code, pre {
		font-family: monospace;
		background-color: #E6E6E6;
		white-space: pre-wrap;
	}
	td {
		border: 0.01rem solid;
		vertical-align: text-top;
		padding: 1rem;
	}

	th {
		border: 0.01rem solid;
	}
	</style>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
	<meta name="robots" content="noindex, nofollow, noarchive">

</head>
<body>

	<h2><u><a href="?">file uploader</a></u></h2>

ENDEND;
	
}

function printFooter(){
	
	echo<<<ENDEND
	
	<hr>

</body>
</html>

ENDEND;

	exit(0);
}

function printUploadForm(){
	echo <<<ENDEND

	<table><tr><td>
	<form action="#" method="post" enctype="multipart/form-data">
	  	<input type="file" id="file" name="files[]" multiple="multiple" onchange="javascript:updateList()" />
		<input type="submit" name="submit" value="Upload!" />
	</form></td></tr></table>
	
ENDEND;
	
}

function processFileUpload(){
	$phpFileUploadErrors = array(
		0 => 'there is no error, the file uploaded with success',
		1 => 'the uploaded file exceeds the upload_max_filesize directive in php.ini',
		2 => 'the uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
		3 => 'the uploaded file was only partially uploaded',
		4 => 'no file was uploaded',
		6 => 'missing a temporary folder',
		7 => 'failed to write file to disk.',
		8 => 'a PHP extension stopped the file upload.',
	); 
	
	echo "	<table><tr><td>";
	if($_FILES["files"]["name"][0] === "" || !isset($_FILES["files"])){
		echo "No files uploaded.<br>";
	} else {
		foreach($_FILES['files']['name'] as $f => $name){
			$newname = date("Y-m-d_H-i-s", time());
			$newname .= '--';
			$newname .= substr(preg_replace('/\s+/', '', pathinfo($name,  PATHINFO_FILENAME)), 0, 20);
			$newname .= '.';
			$newname .= pathinfo($name, PATHINFO_EXTENSION);
			
			echo "<b>$name: </b>";
			if($_FILES['files']['error'][$f] != 0){
				echo "Unable to upload this file [" . $phpFileUploadErrors[$_FILES['files']['error'][$f]] . "].";
				continue;
			}
			if(UPL_FILE_SIZE_LIMIT){
				if($_FILES['files']['size'][$f] > UPL_MAX_FILE_SIZE){
					echo "Unable to upload this file [file too large].";
					continue;
				}
			}
			if(UPL_FILE_ALLOWANCES){
				if(!in_array(pathinfo($name, PATHINFO_EXTENSION), UPL_VALID_FORMATS)){
					echo "Unable to upload this file [file extension not permitted].";
					continue;
				}
			}
			if(UPL_FILE_EXCLUSIONS){
				if(in_array(pathinfo($name, PATHINFO_EXTENSION), UPL_INVALID_FORMATS)){
					echo "Unable to upload this file [file extension not permitted].";
					continue;
				}
			}
			move_uploaded_file($_FILES["files"]["tmp_name"][$f], UPL_UPLOAD_DIR . $newname);
			echo "Uploaded as <code>" . $newname . "</code>.<br>";
		}
	}
	echo "</td></tr></table>";
}



function iniSettings(){
	$cookieLifetime = 60*60*24*7; //7 days
	ini_set( 'session.use_only_cookies', 	true);	// Use only cookies for session IDs
	ini_set( 'session.use_strict_mode', 	true);	// Accept only valid session IDs
	ini_set( 'session.use_trans_sid', 		false);	// Do not attach session ID to URLs
	ini_set( 'session.cookie_httponly', 	true);	// Refuse access to session cookies from JS
	ini_set( 'session.sid_length', 			48);			// Session ID length
	ini_set( 'session.cookie_samesite', 	"strict");		// Strict samesite
	ini_set( 'session.gc_maxlifetime', 		$cookieLifetime);	// Cookie lifetime
	ini_set( 'session.cookie_lifetime', 	$cookieLifetime);	// Cookie lifetime
	session_set_cookie_params($cookieLifetime);

}