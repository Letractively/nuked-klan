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
    die ("<div style=\"text-align: center;\">You cannot open this page directly</div>");
}

translate("modules/Textbox/lang/" . $language . ".lang.php");
require_once("Includes/nkCaptcha.php");

// On determine si le captcha est actif ou non
if (_NKCAPTCHA == "off") $captcha = 0;
else if ((_NKCAPTCHA == 'auto' OR _NKCAPTCHA == 'on') && $user[1] > 0)  $captcha = 0;
else $captcha = 1;

if ($user)
{
    $visiteur = $user[1];
}
else
{
    $visiteur = 0;
}

$level_access = nivo_mod("Textbox");

if ($_SERVER['HTTP_REFERER'])
{
    $redirection = $_SERVER['HTTP_REFERER'];
}
else
{
    $redirection = "index.php";
}


if ($visiteur >= $level_access && $level_access > -1)
{
    opentable();

    if ($captcha == 1 && !ValidCaptchaCode($_REQUEST['code_confirm']))
	{
		echo "<br /><br /><div style=\"text-align: center;\">" . _BADCODECONFIRM . "<br /><br /><a href=\"javascript:history.back()\">[ <b>" . _BACK . "</b> ]</a></div><br /><br />";
		closetable();
		footer();
		exit();
    }

    if (isset($user[2]))
    {
        $pseudo = $user[2];
    }
    else
    {	
    	$_REQUEST['auteur'] =  utf8_decode($_REQUEST['auteur']);
        $_REQUEST['auteur'] = htmlentities($_REQUEST['auteur'], ENT_QUOTES);
        $_REQUEST['auteur'] = verif_pseudo($_REQUEST['auteur']);

        if ($_REQUEST['auteur'] == "error1")
        {
            echo "<br /><br /><div style=\"text-align: center;\">" . _PSEUDOFAILDED . "</div><br /><br />";
            redirect($redirection, 2);
            closetable();
            footer();
            exit();

        }
        else if ($_REQUEST['auteur'] == "error2")
        {
            echo "<br /><br /><div style=\"text-align: center;\">" . _RESERVNICK . "</div><br /><br />";
            redirect($redirection, 2);
            closetable();
            footer();
            exit();
        }
        else if ($_REQUEST['auteur'] == "error3")
        {
            echo "<br /><br /><div style=\"text-align: center;\">" . _BANNEDNICK . "</div><br /><br />";
            redirect($redirection, 2);
            closetable();
            footer();
            exit();
        }
        else
        {
            $pseudo = $_REQUEST['auteur'];
        }
    }

    $sql2 = mysql_query("SELECT auteur, ip, date FROM " . TEXTBOX_TABLE . " ORDER BY id DESC LIMIT 0, 1");
    list($author, $flood_ip, $flood_date) = mysql_fetch_array($sql2);

    $anti_flood = $flood_date + 5;

    $date = time();

	$_REQUEST['texte'] =  utf8_decode($_REQUEST['texte']);

    $_REQUEST['texte'] = mysql_real_escape_string(stripslashes($_REQUEST['texte']));
    $pseudo = mysql_real_escape_string(stripslashes($pseudo));

    if ($user_ip == $flood_ip && $date < $anti_flood && $visiteur == 0)
    {
        echo "<br /><br /><div style=\"text-align: center;\">" . _NOFLOOD . "</div><br /><br />";
        redirect($redirection, 2);
    }

    else if ($_REQUEST['texte'] != "")
    {
        $sql = mysql_query("INSERT INTO " . TEXTBOX_TABLE . " ( `id` , `auteur` , `ip` , `texte` , `date` ) VALUES ( '' , '" . $pseudo . "' ,'" . $user_ip . "' , '" . $_REQUEST['texte'] . "' , '" . $date . "' )");
        echo "<br /><br /><div style=\"text-align: center;\">" . _SHOUTSUCCES . "</div><br /><br />";
        redirect($redirection, 2);
    }

    else
    {
        echo "<br /><br /><div style=\"text-align: center;\">" . _NOTEXT . "</div><br /><br />";
        redirect($redirection, 2);
    }
}
else
{
        echo "<br /><br /><div style=\"text-align: center;\">" . _NOENTRANCE . "</div><br /><br />";
        redirect($redirection, 2);
}

closetable();

?>