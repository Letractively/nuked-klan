<?php
//-------------------------------------------------------------------------//
//  Nuked-KlaN - PHP Portal                                                //
//  http://www.nuked-klan.org                                              //
//-------------------------------------------------------------------------//
//  This program is free software. you can redistribute it and/or modify   //
//  it under the terms of the GNU General Public License as published by   //
//  the Free Software Foundation; either version 2 of the License.         //
//-------------------------------------------------------------------------//
defined('INDEX_CHECK') or die ('You can\'t run this file alone.');

global $nuked, $language;
translate('modules/Search/lang/' . $language . '.lang.php');
translate('modules/404/lang/' . $language . '.lang.php');

opentable();

if($_REQUEST['op'] != 'sql')
{
	echo '<div style="text-align: center; padding-top: 10px"><big><b>' . $nuked['name'] . '</b></big><br /><br />
	' . _NOEXIST . '<br /><br />
	<form method="post" action="index.php?file=Search&amp;op=mod_search">
	<p><input type="hidden" name="module" value="" /><input type="text" name="main" size="25" /></p>
	<p><input type="submit" class="button" name="submit" value="' . _SEARCHFOR . '" /></p>
	<p><a href="index.php?file=Search"><b>' . _ADVANCEDSEARCH . '</b></a> - <a href="javascript:history.back()"><b>' . _BACK . '</b></a></form></div>';
}
else
{
	echo '<div style="text-align: center; padding-top: 10px">' . _ERROR404SQL . '<br /><br />
	<form method="post" action="index.php?file=Search&amp;op=mod_search">
	<input type="hidden" name="module" value="" /><input type="text" name="main" size="25" />
	<input type="submit" class="button" name="submit" value="' . _SEARCHFOR . '" />
	<a href="index.php?file=Search">' . _ADVANCEDSEARCH . '</a></form></div>';
}

closetable();
?>
