<?php
// -------------------------------------------------------------------------//
// Nuked-KlaN - PHP Portal                                                  //
// http://www.nuked-klan.org                                                //
// -------------------------------------------------------------------------//
// This program is free software. you can redistribute it and/or modify     //
// it under the terms of the GNU General Public License as published by     //
// the Free Software Foundation; either version 2 of the License.           //
// -------------------------------------------------------------------------//

define('INDEX_CHECK', 1);

$mtime = microtime();

include_once('Includes/php51compatibility.php');
include('globals.php');
if(file_exists('conf.inc.php')) include('conf.inc.php');

// POUR LA COMPATIBILITE DES ANCIENS THEMES ET MODULES - FOR COMPATIBITY WITH ALL OLD MODULE AND THEME
if (defined('COMPATIBILITY_MODE') && COMPATIBILITY_MODE == TRUE) extract($_REQUEST);

if (!defined('NK_INSTALLED')){
    if (file_exists('install.php')){
        header('location: install.php');
        exit();
    }
}
else{
    if (file_exists('update.php') && file_exists('install.php') || file_exists('update.php') || file_exists('install.php')){
        echo '<div style="text-align: center; margin-top: 15px"><big>Warning ! <b>install.php</b> and <b>update.php</b> must be removed before continuing !</big></div>';
        exit();
    }
}

if (!defined('NK_OPEN')){
    echo '<div style="text-align: center; margin-top: 15px"><big>Sorry, this website is momently closed, Please try again later.</big></div>';
    exit();
}

include('nuked.php');
include_once('Includes/hash.php');

// GESTION DES ERREURS SQL - SQL ERROR MANAGEMENT
set_error_handler('erreursql');

$session = session_check();
if ($session == 1) $user = secure();
else $user = array();

$session_admin = admin_check();
$check_ip = banip();

if (!empty($check_ip)){
    $url_ban = 'ban.php?ip_ban=' . $check_ip;
    redirect($url_ban, 0);
    exit();
}

if (isset($_REQUEST['nuked_nude']) && isset($_REQUEST['nuked_nude'])) $_REQUEST['im_file'] = $_REQUEST['nuked_nude'];
else if (isset($_REQUEST['page']) && isset($_REQUEST['page'])) $_REQUEST['im_file'] = $_REQUEST['page'];
else $_REQUEST['im_file'] = 'index';

if (preg_match('`\.\.`', $theme) || preg_match('`\.\.`', $language) || preg_match('`\.\.`', $_REQUEST['file']) || preg_match('`\.\.`', $_REQUEST['im_file']) || preg_match('`http\:\/\/`i', $_REQUEST['file']) || preg_match('`http\:\/\/`i', $_REQUEST['im_file'] || strpos( $_SERVER['QUERY_STRING'], '..' ) || strpos( $_SERVER['QUERY_STRING'], 'http://' ) || strpos( $_SERVER['QUERY_STRING'], '%3C%3F' ))){
    die('<div style="text-align: center; margin-top: 15px"><big>What are you trying to do ?</big></div>');
}

$_REQUEST['file'] = basename(trim($_REQUEST['file']));
$_REQUEST['im_file'] = basename(trim($_REQUEST['im_file']));
$_REQUEST['page'] = basename(trim($_REQUEST['im_file']));
$theme = trim($theme);
$language = trim($language);

if (!$user){
    $visiteur = 0;
    $_SESSION['admin'] = false;
}
else $visiteur = $user[1];

include ('themes/' . $theme . '/colors.php');
translate('lang/' . $language . '.lang.php');

if ($nuked['nk_status'] == 'closed' && $user[1] < 9 && $_REQUEST['op'] != 'login_screen' && $_REQUEST['op'] != 'login_message' && $_REQUEST['op'] != 'login'){
    echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">
    <head><title>' , $nuked['name'] , ' - ' , $nuked['slogan'] , '</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <link title="style" type="text/css" rel="stylesheet" href="themes/' , $theme , '/style.css" />
    <body style="background: ' , $bgcolor2 , '">
    <div style="width: 600px; padding: 25px; margin: 200px auto; border: 1px solid ' , $bgcolor3 , '; background: ' , $bgcolor1 , '; text-align: center">
    <h2 style="margin: 0">' , $nuked['name'] , ' - ' , $nuked['slogan'] , '</h2>
    ' , _SITECLOSED , '<br/><br /><a href="index.php?file=User&amp;op=login_screen"><b>' . _LOGINUSER . '</b></a></div></body></html>';
}
else if (($_REQUEST['file'] == 'Admin' || $_REQUEST['page'] == 'admin' || (isset($_REQUEST['nuked_nude']) && $_REQUEST['nuked_nude'] == 'admin')) && $_SESSION['admin'] == 0){
    include ('themes/' . $theme . '/theme.php');

    if (!isset($_REQUEST['nuked_nude'])) top();
    include('modules/Admin/login.php');

    if (!isset($_REQUEST['nuked_nude'])){
        footer();
        include('Includes/copyleft.php');
    }
}
else if (($_REQUEST['file'] != 'Admin' AND $_REQUEST['page'] != 'admin') || ( nivo_mod($_REQUEST['file']) === false || (nivo_mod($_REQUEST['file']) > -1 && (nivo_mod($_REQUEST['file']) <= $visiteur))) ){
    include ('themes/' . $theme . '/theme.php');

    if ($nuked['level_analys'] != -1) visits();

    if (!isset($_REQUEST['nuked_nude'])){
        if (defined('NK_GZIP') && ini_get('zlib_output')){
            ob_start('ob_gzhandler');
        }

        if (!($_REQUEST['file'] == 'Admin' || $_REQUEST['page'] == 'admin' || (isset($_REQUEST['nuked_nude']) && $_REQUEST['nuked_nude'] == 'admin')) || $_REQUEST['page'] == 'login') top();
		echo '<script type="text/javascript" src="media/js/infobulle.js"></script>',"\n"
		, '<script type="text/javascript">InitBulle(\'' , $bgcolor2 , '\', \'' , $bgcolor3 , '\', 2);</script>',"\n"
		, '<script type="text/javascript" src="media/ckeditor/ckeditor.js"></script>',"\n"
		, '<script type="text/javascript">',"\n"
		, 'CKEDITOR.replaceClass = \'editoradvanced\';',"\n"
		, '</script>',"\n";

        $sql = mysql_query('SELECT nom FROM ' . MODULES_TABLE);
        $signs = array();
        while($row = mysql_fetch_assoc($sql)){
            $signs[] = "'".Module_Hash($row['nom'])."'";
        }
        if (($_REQUEST['op'] == 'index' || $_REQUEST['op'] == '') && $_REQUEST['im_file'] == 'index' && $_REQUEST['file'] != 'Admin' && $visiteur == 9)
        echo '<script type="text/javascript" src="media/js/update.js"></script>',"\n"
            , '<script type="text/javascript">',"\n"
            , 'NKUpdate.lng = \'',$language,"';\n"
            , 'NKUpdate.UpdateUrl = \'' , UPDATE_URL , "';\n"
            , '</script>',"\n"
            , '<div style="background-color:',$bgcolor1,';padding:10px;border:double 4px ',$bgcolor3,';width:auto;text-align:center;display:none;" id="NKmess"></div>',"\n"
            , '<script type="text/javascript">',"\n"
            , 'NKMessState = 0;NKMessUpdate = 0;',"\n",'NKUpdate.SetModCallback(function(tab){',"\n"
            , 'if ((tab[\'State\'] == \'pirate\' || tab[\'State\'] == \'faille\') && NKMessState == 0) {',"\n"
            , 'document.getElementById(\'NKmess\').innerHTML = document.getElementById(\'NKmess\').innerHTML + \'<br />Un ou plusieurs de vos modules pr�sentent des failles ! \';',"\n"
            , 'NKMessState = 1;',"\n}\n",' if (tab[\'UpdateFile\'] != null) {'
            , 'document.getElementById(\'NKmess\').innerHTML = document.getElementById(\'NKmess\').innerHTML + \'<br />Des mises � jour sont disponible pour un ou plusieurs de vos modules\';',"\n"
            , 'NKMessUpdate = 1;',"\n}\n",' if (((tab[\'State\'] != \'pirate\' || tab[\'State\'] != \'faille\') && NKMessState != 0 )||(tab[\'UpdateFile\'] == null)) {',"\n",' document.getElementById(\'NKmess\').style.display=\'none\';',"\n}\n}\n, new Array(".implode(', ', $signs)."));\n"
            , '</script>',"\n";


        if ($nuked['nk_status'] == 'closed' && $user[1] == 9 && $_REQUEST['file'] != 'Admin' && $_REQUEST['page'] != 'admin'){
            echo '<div style="border: 1px solid ' , $bgcolor3 , '; background: ' , $bgcolor2 , '; margin: 10px; padding: 10px"><b>' , _YOURSITEISCLOSED , ' :<br /><br/ >' , $nuked['url'] , '/index.php?file=User&amp;op=login_screen</b></div>',"\n";
        }
    }

    if (is_file('modules/' . $_REQUEST['file'] . '/' . $_REQUEST['im_file'] . '.php')){
        include('modules/' . $_REQUEST['file'] . '/' . $_REQUEST['im_file'] . '.php');
    }
    else include('modules/404/index.php');

    if (!isset($_REQUEST['nuked_nude'])){
        if ($user[5] > 0 && !isset($_COOKIE['popup']) && $_REQUEST['file'] != 'User' && $_REQUEST['file'] != 'Userbox'){
            echo '<div id="popup_dhtml" style="position:absolute;top:0;left:0;visibility:visible;z-index:10"></div>',"\n"
            , '<script type="text/javascript" src="media/js/popup.js"></script>',"\n"
            , '<script type="text/javascript">popup("' , $bgcolor2 , '", "' , $bgcolor3 , '", "' , _NEWMESSAGESTART , '&nbsp;' , $user[5] , '&nbsp;' , _NEWMESSAGEEND , '", "' , _CLOSEWINDOW , '", "index.php?file=Userbox", 350, 100);</script>',"\n";
        }
        
        if (!($_REQUEST['file'] == 'Admin' || $_REQUEST['page'] == 'admin') || $_REQUEST['page'] == 'login'){
            footer();
        }

        include('Includes/copyleft.php');
        $mtime = microtime() - $mtime;
        echo '<p style="color:#555555;text-align:center;width:100%;">Generated in ',${mtime},'s</p></body></html>';
    }
}
else{
    include ('themes/' . $theme . '/colors.php');
    include ('themes/' . $theme . '/theme.php');
    top();
    opentable();
    translate('lang/' . $language . '.lang.php');
    echo '<br /><br /><div style="text-align: center;">' , _NOENTRANCE , '<br /><br /><a href="javascript:history.back()"><b>' , _BACK , '</b></a></div><br /><br />';
    closetable();
    footer();
}

mysql_close($db);
?>