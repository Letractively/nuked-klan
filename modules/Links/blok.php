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


global $language, $nuked, $theme;
translate("modules/Links/lang/" . $language . ".lang.php");

$sql2 = mysql_query("SELECT active FROM " . BLOCK_TABLE . " WHERE bid = '" . $bid . "'");
list($active) = mysql_fetch_array($sql2);
if ($active == 3 || $active == 4)
{
    if (is_file("themes/" . $theme . "/images/liens.gif"))
    {
	$img = "<img src=\"themes/" . $theme . "/images/liens.gif\" alt=\"\" />";
    }
    else
    {
	$img = "<img src=\"modules/Links/images/liens.gif\" alt=\"\" />";
    }

    echo "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" width=\"90%\">\n"
    . "<tr><td style=\"width: 45%;\" valign=\"top\">" . $img . "&nbsp;<a href=\"index.php?file=Links&amp;op=classe&amp;orderby=news\"><big><b>" . _LAST10LINKS . "</b></big></a><br /><br />\n";

    $i = 0;
    $sql = mysql_query("SELECT id, titre, date, cat FROM " . LINKS_TABLE . " ORDER BY date DESC LIMIT 0, 10");
    while (list($link_id, $titre, $date, $cat) = mysql_fetch_array($sql))
    {
        $titre = htmlentities($titre);
        $date = strftime("%x", $date);

        $sql4 = mysql_query("SELECT titre, parentid FROM " . LINKS_CAT_TABLE . " WHERE cid = '" . $cat . "'");
        list($cat_name, $parentid) = mysql_fetch_array($sql4);
        $cat_name = htmlentities($cat_name);

        if ($cat == 0)
	{
            $category = "";
	}
        else if ($parentid > 0)
        {
            $sql5 = mysql_query("SELECT titre FROM " . LINKS_CAT_TABLE . " WHERE cid = '" . $parentid . "'");
            list($parent_name) = mysql_fetch_array($sql5);
            $parent_name = htmlentities($parent_name);

            $category = $parent_name. " - " . $cat_name;
        }
        else
        {
            $category = $cat_name;
        }

        $i++;

        echo "<b>" . $i . " . <a href=\"index.php?file=Links&amp;op=description&amp;link_id=" . $link_id . "\" style=\"text-decoration: underline\">" . $titre . "</a></b><br />\n";

        if ($category != "") echo $category . "<br />\n";
    }

    echo "</td><td style=\"width: 10%;\">&nbsp;</td><td style=\"width: 45%;\" align=\"left\" valign=\"top\">" . $img . "&nbsp;<a href=\"index.php?file=Links&amp;op=classe&amp;orderby=count\"><big><b>" . _TOP10LINKS . "</b></big></a><br /><br />\n";

    $l = 0;
    $sql3 = mysql_query("SELECT id, titre, cat FROM " . LINKS_TABLE . " ORDER BY count DESC LIMIT 0, 10");
    while (list($tlink_id, $ttitre, $tcat) = mysql_fetch_array($sql3))
    {
        $ttitre = htmlentities($ttitre);

        $sql4 = mysql_query("SELECT titre, parentid FROM " . LINKS_CAT_TABLE . " WHERE cid = '" . $tcat . "'");
        list($tcat_name, $tparentid) = mysql_fetch_array($sql4);
        $tcat_name = htmlentities($tcat_name);

        if ($tcat == 0)
	{
            $tcategory = "";
	}
        else if ($parentid > 0)
        {
            $sql5 = mysql_query("SELECT titre FROM " . LINKS_CAT_TABLE . " WHERE cid = '" . $tparentid . "'");
            list($tparent_name) = mysql_fetch_array($sql5);
            $tparent_name = htmlentities($tparent_name);

            $tcategory = $tparent_name . " - " . $tcat_name;
        }
        else
        {
            $tcategory = $tcat_name;
        }

        $l++;

        echo "<b>" . $l . " . <a href=\"index.php?file=Links&amp;op=description&amp;link_id=" . $tlink_id . "\" style=\"text-decoration: underline\">" . $ttitre . "</a></b><br />\n";

        if ($tcategory != "") echo $tcategory . "<br />\n";
    }

    echo "</td></tr><tr><td style=\"width: 45%;\" align=\"right\"><a href=\"index.php?file=Links&amp;op=classe&amp;orderby=news\"><small>+ " . _MORELAST . "</small></a></td>\n"
    . "<td style=\"width: 10%;\"></td><td style=\"width: 45%;\" align=\"right\"><a href=\"index.php?file=Links&amp;op=classe&amp;orderby=count\"><small>+ " . _MORETOP . "</small></a></td></tr></table>\n";
}

else
{
    $i = 0;
    $sql = mysql_query("SELECT id, titre, date FROM " . LINKS_TABLE . " ORDER BY date DESC LIMIT 0, 10");
    while (list($link_id, $titre, $date) = mysql_fetch_array($sql))
    {
        $titre = htmlentities($titre);
        $date = strftime("%x", $date);
        $i++;

        echo "<div><b>" . $i . " . <a href=\"index.php?file=Links&amp;op=description&amp;link_id=" . $link_id . "\">" . $titre . "</a></b> (" . $date . ")</div>\n";
    }
}

?>