<?php
// -------------------------------------------------------------------------//
// Nuked-KlaN - PHP Portal                                                  //
// http://www.nuked-klan.org                                                //
// -------------------------------------------------------------------------//
// This program is free software. you can redistribute it and/or modify     //
// it under the terms of the GNU General Public License as published by     //
// the Free Software Foundation; either version 2 of the License.           //
// -------------------------------------------------------------------------//
if (!defined("INDEX_CHECK"))
{
	exit('You can\'t run this file alone.');
}

/**
* Delete all global vars
*
* @return void
*/
function DeleteGlobalVars(){
	//Vars witch shouldn't be delete
	$NoDelete = array('GLOBALS', '_POST', '_GET', '_COOKIE', '_FILES', '_SERVER', '_ENV', '_REQUEST', '_SESSION');

	foreach ($GLOBALS as $k=>$v){
		if (in_array($k, $NoDelete) === false) {
			$GLOBALS[$k] = NULL;
			unset($GLOBALS[$k]);
		}
	}
}


/**
* Secure a var
*
* @param mixed var
* @return void
*/

function SecureVar($value){
	if (is_array($value)) {
		foreach ($value as $k=>$v){
			$value[$k] = SecureVar($value[$k]);
		}
		return $value;
	}
	else
	{
		return str_replace(array('<', '>', '0x'), array('&lt;', '&gt;', '\0x'), addslashes($value)) ;
	}
}
error_reporting (E_ERROR | E_WARNING | E_PARSE);
set_magic_quotes_runtime(0);

// ANTI INJECTION SQL (UNION) et XSS/CSS
$query_string = strtolower(rawurldecode($_SERVER['QUERY_STRING']));
$bad_string = array("%20union%20", "/*", "*/union/*", "+union+", "load_file", "outfile", "document.cookie", "onmouse", "<script", "<iframe", "<applet", "<meta", "<style", "<form", "<img", "<body", "<link");
foreach ($bad_string as $string_value)
{
    if (strpos($query_string, $string_value)) die("<br /><br /><br /><div style=\"text-align: center;\"><big>What are you trying to do ?</big></div>");
}
unset($query_string, $bad_string, $string_value);

$get_id = array("news_id", "cat_id", "cat", "forum_id", "thread_id", "dl_id", "link_id", "cid", "secid", "artid", "poll_id", "sid", "vid", "im_id", "tid", "game", "war_id", "server_id", "mid", "p", "m", "y", "mo", "ye", "oday", "omonth", "oyear");
foreach ($get_id as $int_id)
{
    if (isset($_GET[$int_id]) && $_GET[$int_id] != "" && !is_numeric($_GET[$int_id])) die("<br /><br /><br /><div style=\"text-align: center;\"><big>Error : ID must be a number !</big></div>");
}
unset($get_id, $int_id);

// FONCTION DE SUBSTITUTION POUR MAGIC_QUOTE_GPC
DeleteGlobalVars();
if (!get_magic_quotes_gpc())
{
	$_GET = array_map('SecureVar', $_GET);
	$_POST = array_map('SecureVar', $_POST);
	$_COOKIE = array_map('SecureVar', $_COOKIE);
}
$_REQUEST = array_merge($_COOKIE, $_POST, $_GET);
foreach ($_FILES as $k=>$v)
{
	if(!empty($_FILES[$k]['name']))
	{
		$_FILES[$k]['name'] = substr(md5(uniqid()), rand(0, 20), 10) . strrchr($_FILES[$k]['name'], '.');
		if (($_REQUEST['file'] == 'Gallery' || $_REQUEST['file'] == 'User') && !is_file($_FILES[$k]['name']))
			die ('Upload this file isn\'t autorised !!');
		else
		{
			$sfile = new finfo(FILEINFO_MIME);
			if (strpos(strtolower(strrchr($_FILES[$k]['name'], '.')), 'php') !== false
				|| strpos(strtolower($sfile->file($_FILES[$k]['tmp_name'])), 'php') !== false)
			{
				die ('Upload a PHP file isn\'t autorised !!');
			}
			unset($sfile);
		}
	}
}

//register_shutdown_function(create_function('', 'var_dump($_GET, $_POST, $_REQUEST);return false;'));
?>