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

global $nuked, $language, $user;
translate("modules/Links/lang/" . $language . ".lang.php");

if (!$user)
{
    $visiteur = 0;
} 
else
{
    $visiteur = $user[1];
} 
$ModName = basename(dirname(__FILE__));
$level_access = nivo_mod($ModName);
if ($visiteur >= $level_access && $level_access > -1)
{

    include ("modules/Vote/index.php");
    compteur("Links");

    function index()
    {
        global $nuked;

        opentable();

        echo "<br /><div style=\"text-align: center;\"><big><b>" . _WEBLINKS . "</b></big></div>\n"
	. "<div style=\"text-align: center;\"><br />\n"
	. "[ " . _INDEXLINKS . " | "
	. "<a href=\"index.php?file=Links&amp;op=classe&amp;orderby=news\" style=\"text-decoration: underline\">" . _NEWSLINK . "</a> | "
	. "<a href=\"index.php?file=Links&amp;op=classe&amp;orderby=count\" style=\"text-decoration: underline\">" . _TOPLINKS . "</a> | "
	. "<a href=\"index.php?file=Suggest&amp;module=Links\" style=\"text-decoration: underline\">" . _SUGGESTLINK . "</a> ]</div>\n";

        $sql = mysql_query("SELECT id FROM " . LINKS_TABLE);
        $nb_links = mysql_num_rows($sql);

        $sql_nbcat = mysql_query("SELECT cid FROM " . LINKS_CAT_TABLE);
        $nb_cat = mysql_num_rows($sql_nbcat);
        if ($nb_cat > 0)
        {
            echo "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" cellspacing=\"15\" cellpadding=\"5\">\n";

            $sql_cat = mysql_query("SELECT cid, titre, description FROM " . LINKS_CAT_TABLE . " WHERE parentid = 0 ORDER BY position, titre");
            $test = 0;
            while (list($cid, $titre, $description) = mysql_fetch_array($sql_cat))
            {
                $titre = htmlentities($titre);

                $description = icon($description);

                if ($cid != $last_cid)
                {
                    $test++;
                    if ($test == 1)
                    {
                        echo "<tr>";
                    } 

                    echo "<td valign=\"top\"><img src=\"modules/Links/images/fleche.gif\" alt=\"\" /><a href=\"index.php?file=Links&amp;op=categorie&amp;cat=" . $cid . "\"><b>" . $titre . "</b></a>";

                    $sql2 = mysql_query("SELECT cat FROM " . LINKS_TABLE . " WHERE cat = '" . $cid . "'");
                    $nb_lk = mysql_num_rows($sql2);

                    if ($nb_lk > 0)
                    {
                        echo "<small>&nbsp;(" . $nb_lk . ")</small>\n";
                    } 
		    
                    if ($description != "")
                    {
                        echo "<div style=\"width: 225px;\">" . $description . "</div>\n";
                    } 
                    else
                    {
                        echo "<br />";
                    }

                    $t = 0;
                    $sql_subcat = mysql_query("SELECT cid, titre FROM " . LINKS_CAT_TABLE . " WHERE parentid = '" . $cid . "' ORDER BY position, titre LIMIT 0, 4");
                    while (list($sub_cat_id, $sub_cat_titre) = mysql_fetch_array($sql_subcat))
                    {
                        $sub_cat_titre = htmlentities($sub_cat_titre);
                        $t++;
                        if ($t <= 3) echo "<small><a href=\"index.php?file=Links&amp;op=categorie&amp;cat=" . $sub_cat_id . "\">" . $sub_cat_titre . "</a></small>&nbsp;&nbsp;";
                        else echo "<a href=\"index.php?file=Links&amp;op=categorie&amp;cat=" . $cid . "\"><small>...</small></a>";
                    } 

                    echo "</td>\n";

                    if ($test == 2)
                    {
                        $test = 0;
                        echo "</tr>\n";
                    } 
                    $last_cid = $cid;
                } 
            } 

            if ($test == 1) echo "</tr>\n";
            echo "</table>\n";
        } 
        else
	{
            echo "<br />\n";
	}

        classe("0", "0");

        if ($nb_cat > 0 || $nb_links > 0) echo "<div style=\"text-align: center;\"><br /><small><i> ( " . _THEREIS . "&nbsp;" . $nb_links . "&nbsp;" . _LINKS . " &amp; " . $nb_cat . "&nbsp;" . _NBCAT . "&nbsp;" . _INDATABASE . " )</i></small></div><br />\n";
        else echo "<div style=\"text-align: center;\"><br />" . _NOLINKINDB . "</div><br /><br />\n";
    
        closetable();
    } 

    function categorie($cat)
    {
        global $nuked;

        opentable();

        $sql = mysql_query("SELECT titre, description, parentid FROM " . LINKS_CAT_TABLE . " WHERE cid = '" . $cat . "'");
		if(mysql_num_rows($sql) <= 0)
		{
			redirect("index.php?file=404", 0);
			exit();
		}
        list($cat_titre, $cat_desc, $parentid) = mysql_fetch_array($sql);

        $cat_titre = htmlentities($cat_titre);

        $cat_desc = icon($cat_desc);

        if ($parentid > 0)
        {
            $sql_parent = mysql_query("SELECT titre FROM " . LINKS_CAT_TABLE . " WHERE cid = '" . $parentid . "'");
            list($parent_titre) = mysql_fetch_array($sql_parent);
            $parent_titre = htmlentities($parent_titre);

            echo "<br /><div style=\"text-align: center;\"><a href=\"index.php?file=Links\" style=\"text-decoration:none\"><big><b>" . _WEBLINKS . "</b></big></a> &gt; <a href=\"index.php?file=Links&amp;op=categorie&amp;cat=" . $parentid . "\" style=\"text-decoration:none\"><big><b>" . $parent_titre . "</b></big></a> &gt; <big><b>" . $cat_titre . "</b></big></div><br />\n";
        } 
        else
        {
            echo "<br /><div style=\"text-align: center;\"><a href=\"index.php?file=Links\" style=\"text-decoration:none\"><big><b>" . _WEBLINKS . "</b></big></a> &gt; <big><b>" . $cat_titre . "</b></big></div><br />\n";
        } 

        $sql3 = mysql_query("SELECT cid, titre, description  FROM " . LINKS_CAT_TABLE . " WHERE parentid = '" . $cat . "' ORDER BY position, titre");
        $nb_subcat = mysql_num_rows($sql3);
        $count = 0;

    if ($nb_subcat > 0)
    {
        echo "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" cellspacing=\"15\" cellpadding=\"5\">\n";

        while (list($catid, $parentcat, $parentdesc) = mysql_fetch_array($sql3))
        {

            $parentcat = htmlentities($parentcat);
			
            $parentdesc = icon($parentdesc);

            $sql_nbcat = mysql_query("SELECT id FROM " . LINKS_TABLE . " WHERE cat = '" . $catid . "'");
            $nb_lkcat = mysql_num_rows($sql_nbcat);

            if ($catid != $last_catid)
            {
                $count++;

                if ($count == 1)
                {
                    echo "<tr>";
                } 

                echo "<td style=\"width: 225px;\" valign=\"top\"><img src=\"modules/Links/images/fleche.gif\" alt=\"\" /><a href=\"index.php?file=Links&amp;op=categorie&amp;cat=" . $catid . "\"><b>" . $parentcat . "</b></a> <small>(" . $nb_lkcat . ")</small><br />" . $parentdesc . "</td>";

                if ($count == 2)
                {
                    $count = 0;
                    echo "</tr>\n";
                } 

                $last_catid = $catid;
            } 
        } 

        if ($count == 1) echo "</tr>\n";
        echo "</table>\n";

    }
    else
    {
	echo "<div style=\"text-align: center;\">" . $cat_desc . "</div><br />\n";
    } 

        classe($cat, $nb_subcat);

        echo "<br />\n";

        closetable();
    } 

    function do_link($link_id)
    {
        global $nuked;

        $sql = mysql_query("SELECT url, count FROM " . LINKS_TABLE . " WHERE id = '" . $link_id . "'");
		if(mysql_num_rows($sql) <= 0)
		{
			redirect("index.php?file=404", 0);
			exit();
		}
        list($link_url, $count) = mysql_fetch_array($sql);
        $new_count = $count + 1;

        $upd = mysql_query("UPDATE " . LINKS_TABLE . " SET count = ' " . $new_count . "' WHERE id = '" . $link_id . "'");

        header("location: " . $link_url);
    } 


    function broken($link_id)
    {
        global $nuked;

        $sql = mysql_query("UPDATE " . LINKS_TABLE . " SET broke = broke + 1 WHERE id = '" . $link_id . "'");
        opentable();
        echo "<br /><br /><div style=\"text-align: center;\">" . _THXBROKENLINK . "</div><br /><br />";
        closetable();
        redirect("index.php?file=Links", 2);
    } 

    function description($link_id)
    {
        global $nuked, $user, $visiteur, $bgcolor1, $bgcolor2, $bgcolor3;

        $sql = mysql_query("SELECT id, date, titre, description, webmaster, country, cat, count FROM " . LINKS_TABLE . " WHERE id = '" . $link_id . "'");
		if(mysql_num_rows($sql) <= 0)
		{
			redirect("index.php?file=404", 0);
			exit();
		}
        list($link_id, $date, $titre, $description, $webmaster, $country, $cat, $count) = mysql_fetch_array($sql);

        $titre = htmlentities($titre);
        $webmaster = htmlentities($webmaster);

        $description = icon($description);

        $date = nkDate($date);

        $sql2 = mysql_query("SELECT titre, parentid FROM " . LINKS_CAT_TABLE . " WHERE cid = '" . $cat . "'");
        list($cat_name, $parentid) = mysql_fetch_array($sql2);
        $cat_name = htmlentities($cat_name);

        if ($cat == 0)
        {
            $category = _NONE;
        } 
        else if ($parentid > 0)
        {
            $sql3 = mysql_query("SELECT titre FROM " . LINKS_CAT_TABLE . " WHERE cid = '" . $parentid . "'");
            list($parent_name) = mysql_fetch_array($sql3);
            $parent_name = htmlentities($parent_name);

            $category = "<a href=\"index.php?file=Links&amp;op=categorie&amp;cat=" . $parentid . "\">" . $parent_name . "</a> -&gt; <a href=\"index.php?file=Links&amp;op=categorie&amp;cat=" . $cat. "\">" . $cat_name . "</a>";
        } 
        else
        {
            $category = "<a href=\"index.php?file=Links&amp;op=categorie&amp;cat=" . $cat . "\">" . $cat_name . "</a>";
        } 

	if ($country != "" && is_file("images/flags/" . $country))
	{
            list ($pays, $ext) = explode ('.', $country);
            $link_pays = "<img src=\"images/flags/" . $country . "\" alt=\"\" title=\"" . $pays . "\" />";
	}
	else
	{
            $pays = "";
            $link_pays = "&nbsp;";
	}

        opentable();

        if ($visiteur >= admin_mod("Links"))
        {
            echo"<script type=\"text/javascript\">\n"
            ."<!--\n"
            ."\n"
            . "function del_link(titre, id)\n"
            . "{\n"
            . "if (confirm('" . _DELETELINK . " '+titre+' ! " . _CONFIRM . "'))\n"
            . "{document.location.href = 'index.php?file=Links&page=admin&op=del&link_id='+id;}\n"
            . "}\n"
            . "\n"
            . "// -->\n"
            . "</script>\n";

            echo "<div style=\"text-align: right;\"><a href=\"index.php?file=Links&amp;page=admin&amp;op=edit_link&amp;link_id=" . $link_id . "\"><img style=\"border: 0;\" src=\"images/edition.gif\" alt=\"\" title=\"" . _EDIT . "\" /></a>"
            . "&nbsp;<a href=\"javascript:del_link('" . mysql_real_escape_string(stripslashes($titre)) . "', '" . $link_id . "');\"><img style=\"border: 0;\" src=\"images/delete.gif\" alt=\"\" title=\"" . _DEL . "\" /></a></div>\n";
        } 

        echo "<br /><div style=\"text-align: center;\"><a href=\"index.php?file=Links\" style=\"text-decoration:none\"><big><b> " . _WEBLINKS . " </b></big></a></div><br />\n"
	. "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" width=\"80%\" border=\"0\" cellspacing=\"3\" cellpadding=\"3\">\n"
	. "<tr><td style=\"background: " . $bgcolor2 . ";border: 1px solid " . $bgcolor3 . ";\" align=\"center\">\n"
	. "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n"
	. "<tr><td style=\"width: 5%;\">&nbsp;</td>\n"
	. "<td style=\"width: 90%;\" align=\"center\"><big><b>" . $titre . "</b></big></td>\n"
	. "<td style=\"width: 5%;\" align=\"center\">" . $link_pays . "</td></tr></table></td></tr>\n";

        if ($description != "")
        {
            echo "<tr style=\"background: " . $bgcolor1 . ";\"><td style=\"border: 1px dashed " . $bgcolor3 . ";\">" . $description . "</td></tr>\n"
            . "<tr style=\"background: " . $bgcolor2 . ";\"><td>&nbsp;</td></tr>\n";
        } 

        echo "<tr style=\"background: " . $bgcolor1 . ";\"><td style=\"border: 1px dashed " . $bgcolor3 . ";\"><b>" . _CAT . " :</b> " . $category . "</td></tr>\n" 
	. "<tr style=\"background: " . $bgcolor1 . ";\"><td style=\"border: 1px dashed " . $bgcolor3 . ";\"><b>" . _ADDTHE . " :</b> " . $date . "</td></tr>\n";

        if ($webmaster != "")
        {
            echo "<tr style=\"background: " . $bgcolor1 . ";\"><td style=\"border: 1px dashed " . $bgcolor3 . ";\"><b>" . _WEBMASTER . " :</b> " . $webmaster . "</td></tr>\n";
	}

        if ($country != "" && $pays != "")
        {
            echo "<tr style=\"background: " . $bgcolor1 . ";\"><td style=\"border: 1px dashed " . $bgcolor3 . ";\"><b>" . _COUNTRY . " :</b> " . $pays . "</td></tr>\n";
	}

	echo "<tr style=\"background: " . $bgcolor1 . ";\"><td style=\"border: 1px dashed " . $bgcolor3 . ";\"><b>" . _VISIT . " :</b> " . $count . "&nbsp;" . _TIMES . "</td></tr>\n"
	. "<tr style=\"background: " . $bgcolor1 . ";\"><td style=\"border: 1px dashed " . $bgcolor3 . ";\">";

        vote_index("Links", $link_id);

        echo "</td></tr>\n";
		if ($visiteur > 0) echo "<tr style=\"background: " . $bgcolor2 . ";\"><td>&nbsp;</td></tr>\n"
		. "<tr style=\"background: " . $bgcolor1 . ";\"><td style=\"border: 1px dashed " . $bgcolor3 . ";\"><img src=\"modules/Links/images/warning.gif\" alt=\"\" /> [ <a href=\"index.php?file=Links&amp;op=broken&amp;link_id=" . $link_id . "\">" . _INDICATELINK . "</a> ]</td></tr>";
		echo "</table>\n"
		. "<div style=\"text-align: center;\"><br /><input type=\"button\" value=\"" . _VISITTHISSITE . "\" onclick=\"window.open('index.php?file=Links&amp;nuked_nude=index&amp;op=do_link&amp;link_id=" . $link_id . "')\" /></div><br />";
		
		$sql = mysql_query("SELECT active FROM " . $nuked['prefix'] . "_comment_mod WHERE module = 'links'");
		list($active) = mysql_fetch_array($sql);
			
		if($active == 1)
		{
		
		echo "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" width=\"80%\" border=\"0\" cellspacing=\"3\" cellpadding=\"3\"><tr style=\"background: " . $bgcolor1 . ";\"><td style=\"border: 1px dashed " . $bgcolor3 . ";\">";

            include ("modules/Comment/index.php");
            com_index("Links", $link_id);

            echo "</td></tr></table>\n";
		}
        closetable();
    } 

    function classe($cat, $nb_subcat)
    {
        global $nuked, $theme, $bgcolor1, $bgcolor2, $bgcolor3;

        if ($_REQUEST['op'] == "classe")
        {
            echo "<br /><div style=\"text-align: center;\"><big><b>" . _WEBLINKS . "</b></big></div>\n"
            . "<div style=\"text-align: center;\"><br />\n"
            . "[ <a href=\"index.php?file=Links\" style=\"text-decoration: underline\">" . _INDEXLINKS . "</a> | ";

            if ($_REQUEST['orderby'] == "news")
            {
                echo _NEWSLINK . " | ";
            } 
            else
            {
                echo "<a href=\"index.php?file=Links&amp;op=classe&amp;orderby=news\" style=\"text-decoration: underline\">" . _NEWSLINK . "</a> | ";
            } 
            if ($_REQUEST['orderby'] == "count")
            {
                echo _TOPLINKS . " | ";
            } 
            else
            {
                echo "<a href=\"index.php?file=Links&amp;op=classe&amp;orderby=count\" style=\"text-decoration: underline\">" . _TOPLINKS . "</a> | ";
            } 

            echo "<a href=\"index.php?file=Suggest&amp;module=Links\"  style=\"text-decoration: underline\">" . _SUGGESTLINK . "</a> ]</div><br />\n";
        } 

        $nb_liens = $nuked['max_liens'];
        if (!$_REQUEST['p']) $_REQUEST['p'] = 1;
        $start = $_REQUEST['p'] * $nb_liens - $nb_liens;

        if ($cat != "") $where = "WHERE L.cat = '" . $cat . "'";
	else $where = "";

        if ($_REQUEST['orderby'] == "name")
        {
            $order = "ORDER BY L.titre";
        } 

        else if ($_REQUEST['orderby'] == "count")
        {
            $order = "ORDER BY L.count DESC";
        } 

        else if ($_REQUEST['orderby'] == "note")
        {
            $order = "ORDER BY note DESC";
        } 

        else
        {
            $_REQUEST['orderby'] = "news";
            $order = "ORDER BY L.id DESC";
        } 

        $sql = mysql_query("SELECT L.id, L.date, L.titre, L.description, L.count, L.country, AVG(V.vote) AS note  FROM " . LINKS_TABLE . " AS L LEFT JOIN " . VOTE_TABLE . " AS V ON L.id = V.vid AND V.module = 'Links' " . $where . " GROUP BY L.id " . $order);
        $nb_lk = mysql_num_rows($sql);

        if ($nb_lk > 1 && $cat != "")
        {
            echo "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" width=\"90%\">\n"
            . "<tr><td align=\"right\"><small>" . _ORDERBY . " : ";

            if ($_REQUEST['orderby'] == "news") echo "<b>" . _DATE . "</b> | ";
            else echo "<a href=\"index.php?file=Links&amp;op=" . $_REQUEST['op'] . "&amp;orderby=news&amp;cat=" . $cat . "\">" . _DATE . "</a> | ";

            if ($_REQUEST['orderby'] == "count") echo "<b>" . _TOPFILE . "</b> | ";
            else echo"	<a href=\"index.php?file=Links&amp;op=" . $_REQUEST['op'] . "&amp;orderby=count&amp;cat=" . $cat . "\">" . _TOPFILE . "</a> | ";

            if ($_REQUEST['orderby'] == "name") echo "<b>" . _NAME . "</b> | ";
            else echo "<a href=\"index.php?file=Links&amp;op=" . $_REQUEST['op'] . "&amp;orderby=name&amp;cat=" . $cat . "\">" . _NAME . "</a> | ";

            if ($_REQUEST['orderby'] == "note") echo "<b>" . _NOTE . "</b>";
            else echo "<a href=\"index.php?file=Links&amp;op=" . $_REQUEST['op'] . "&amp;orderby=note&amp;cat=" . $cat . "\">" . _NOTE . "</a>";

            echo "</small></td></tr></table>\n";
        } 

        if ($nb_lk > 0)
        {

            if ($nb_lk > $nb_liens)
            {
                echo "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" width=\"90%\"><tr><td>";
                $url_page = "index.php?file=Links&amp;op=". $_REQUEST['op'] . "&amp;cat=" . $cat . "&amp;orderby=" . $_REQUEST['orderby'];
                number($nb_lk, $nb_liens, $url_page);
                echo "</td></tr></table>\n";
            } 

            echo "<br />";

            $sqlhot = mysql_query("SELECT id FROM " . LINKS_TABLE . " ORDER BY count DESC LIMIT 0, 10");

            $seek = mysql_data_seek($sql, $start);
            for($i = 0;$i < $nb_liens;$i++)
            {
                if (list($link_id, $date, $titre, $description, $count, $country) = mysql_fetch_array($sql))
                {
                    $titre = htmlentities($titre);
                    $newsdate = time() - 604800;
                    $att = "";

                    if ($date != "" && $date > $newsdate) $att = "&nbsp;&nbsp;" . _NEW;

                    if ($description != "")
                    {
                        $description = str_replace("\r", "", $description);
                        $description = str_replace("\n", " ", $description);
                        $texte = strip_tags($description);

                        if (strlen($texte) > 150)
                        {
                            $texte = substr($texte, 0, 150) . "...";
                        } 

                        
                    } 
                    else
                    {
                        $texte = "";
                    } 

                    mysql_data_seek($sqlhot, 0);
                    while (list($id_hot) = mysql_fetch_array($sqlhot))
                    {
                        if ($link_id == $id_hot && $nb_lk > 1 && $count > 9) $att .= "&nbsp;&nbsp;" . _HOT;
                    } 

                    if ($date != "") $alt = "title=\"" . _ADDTHE . "&nbsp;" . nkDate($date) . "\"";
                    else $alt = "";

                    if (is_file("themes/" . $theme . "/images/liens.gif"))
                    {
                        $img = "<img src=\"themes/" . $theme . "/images/liens.gif\" alt=\"\" " . $alt . "/>";
                    } 
                    else
                    {
                        $img = "<img src=\"modules/Links/images/liens.gif\" alt=\"\" " . $alt . "/>";
                    } 

                    if ($country != "" && is_file("images/flags/" . $country))
                    {
                        list ($pays, $ext) = explode ('.', $country);
                        $link_pays = "<img src=\"images/flags/" . $country . "\" alt=\"\" title=\"" . $pays . "\" />";
                    }
                    else
                    {
                        $link_pays = "&nbsp;";
                    }			

                    echo "<table style=\"background: " . $bgcolor3 . ";margin-left: auto;margin-right: auto;text-align: left;\" width=\"90%\" cellspacing=\"1\" cellpadding=\"0\">\n"
                    . "<tr><td><table style=\"background: " . $bgcolor2 . ";\" width=\"100%\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\">\n"
                    . "<tr><td style=\"width: 100%;\">" . $img . "&nbsp;<a href=\"index.php?file=Links&amp;op=description&amp;link_id=" . $link_id . "\"><big><b>" . $titre . "</b></big></a>" . $att . "</td>\n"
                    . "<td>" . $link_pays . "</td></tr>";

                    if ($texte != "")
                    {
                        echo "<tr><td colspan=\"2\">" . $texte . "</td></tr>\n";
                    } 

                    echo "<tr style=\"background: " . $bgcolor1 . ";\"><td colspan=\"2\">&nbsp;<b>" . _HITS . " :</b> " . $count . " &nbsp;";
                    vote_index("Links", $link_id);

                    echo "</td></tr></table></td></tr></table><br />\n";
                } 
            } 

            if ($nb_lk > $nb_liens)
            {
                echo "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" width=\"90%\"><tr><td>";
                $url_page = "index.php?file=Links&amp;op=". $_REQUEST['op'] . "&amp;cat=" . $cat . "&amp;orderby=" . $_REQUEST['orderby'];
                number($nb_lk, $nb_liens, $url_page);
                echo "</td></tr></table>\n";
            } 

        } 
        else
        {
            if ($nb_subcat == 0 && $cat > 0) echo "<div style=\"text-align: center;\"><br />" . _NOLINKS . "</div><br /><br />\n";
            if ($_REQUEST['op'] == "classe") echo "<div style=\"text-align: center;\"><br />" . _NOLINKINDB . "</div><br /><br />\n";
        } 

    } 

    switch ($_REQUEST['op'])
    {
        case "categorie":
            categorie($_REQUEST['cat']);
            break;

        case "classe":
            opentable();
            classe($_REQUEST['cat'], $_REQUEST['nb_subcat']);
            closetable();
            break;

        case "do_link":
            do_link($_REQUEST['link_id']);
            break;

        case "broken":
            broken($_REQUEST['link_id']);
            break;

        case "description":
            description($_REQUEST['link_id']);
            break;

        default:
            index();
            break;
    } 

} 
else if ($level_access == -1)
{
    opentable();
    echo "<br /><br /><div style=\"text-align: center;\">" . _MODULEOFF . "<br /><br /><a href=\"javascript:history.back()\"><b>" . _BACK . "</b></a><br /><br /></div>";
    closetable();
} 
else if ($level_access == 1 && $visiteur == 0)
{
    opentable();
    echo "<br /><br /><div style=\"text-align: center;\">" . _USERENTRANCE . "<br /><br /><b><a href=\"index.php?file=User&amp;op=login_screen\">" . _LOGINUSER . "</a> | <a href=\"index.php?file=User&amp;op=reg_screen\">" . _REGISTERUSER . "</a></b><br /><br /></div>";
    closetable();
} 
else
{
    opentable();
    echo "<br /><br /><div style=\"text-align: center;\">" . _NOENTRANCE . "<br /><br /><a href=\"javascript:history.back()\"><b>" . _BACK . "</b></a><br /><br /></div>";
    closetable();
} 

?>
