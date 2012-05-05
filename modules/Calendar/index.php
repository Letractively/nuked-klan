<?php
// -------------------------------------------------------------------------//
// Nuked-KlaN - PHP Portal                                                  //
// http://www.nuked-klan.org                                                //
// -------------------------------------------------------------------------//
// This program is free software. you can redistribute it and/or modify     //
// it under the terms of the GNU General Public License as published by     //
// the Free Software Foundation; either version 2 of the License.           //
// -------------------------------------------------------------------------//
defined('INDEX_CHECK') or die('<div style="text-align:center;">You cannot open this page directly</div>');

global $language, $user;
translate("modules/Calendar/lang/" . $language . ".lang.php");
$visiteur = $user ? $user[1] : 0;
$ModName = basename(dirname(__FILE__));
$level_access = nivo_mod($ModName);

function index(){
	global $bgcolor1, $bgcolor2, $bgcolor3, $bgcolor4, $user;
	
	$SECONDS_PER_DAY = 60 * 60 * 24;
	
	function is_in_array($value , $array){
		$SizeArray = sizeof($array);
		for ($i = 0 ; $i < $SizeArray; $i++){
			if (!strcmp($value , $array[$i])) return 1;
		}
		return 0;
	}
	
	function cl_trim ($s){
		$ret = preg_replace ("`^[[:space:]]+|[[:space:]]+$`i", "", $s);
		return $ret;
	}
	
	function js_popup($w = 500, $h = 250){
		echo "<script type=\"text/javascript\">\n"
		. "<!--\n"
		. "function openWin(type,id,d,m,y){\n"
		. "w = window.open('index.php?file=Calendar&nuked_nude=index&op=show_event&eid='+id+'&type='+type+'&d='+d+'&m='+m+'&y='+y,'event'+id,'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,width=" . $w . ",height=" . $h . ",top=30,left=0');\n"
		. "}\n"
		. "// -->\n"
		. "</script>";
	}
	
	class month{
		var $months_hash = array("01" => _JAN, "02" => _FEB, "03" => _MAR, "04" => _APR, "05" => _MAY, "06" => _JUN, "07" => _JUL, "08" => _AUG, "09" => _SEP, "10" => _OCT, "11" => _NOV, "12" => _DEC);
		var $month_name;
		var $month_number;
		var $year;
		var $month_data;
		var $nextmonth;
		var $nextyear;
		var $prevmonth;
		var $prevyear;
		
		function month($thismonth = "" , $thisyear = ""){
			global $nuked;
			
			if (!$thismonth) $thismonth = date("m");
			if (!$thisyear) $thisyear = date("Y");
			
			$this->month_name = $this->months_hash[$thismonth];
			$this->month_number = $thismonth;
			$this->year = $thisyear;
			$this->nextmonth = sprintf("%02d", $this->month_number + 1);
			$this->prevmonth = sprintf("%02d", $this->month_number-1);
			$this->nextyear = $this->prevyear = $thisyear;
			
			if ($this->month_number == "12"){
				$this->nextmonth = "01";
				$this->nextyear = $thisyear + 1;
			}
			
			if ($this->month_number == "01"){
				$this->prevmonth = "12";
				$this->prevyear = $thisyear - 1;
			}
			
			$query2 = "SELECT id, date_jour, titre FROM " . CALENDAR_TABLE . " WHERE date_mois = '" . $this->month_number . "' AND date_an = '" . $this->year . "'  ORDER BY date_jour";
			$data2 = mysql_query($query2);
			
			if (!$data2) echo mysql_error() . ": " . mysql_errno();
			
			while ($cmp = mysql_fetch_array($data2)){
				if (strcmp($cmp['titre'] , "") != 0){
					$cmp['titre'] = htmlentities($cmp['titre']);
					$this->month_data[$cmp['date_jour']]['id'][] = $cmp['id'];
					$this->month_data[$cmp['date_jour']]['event_title'][] = "<span title=\"" . $cmp['titre'] . "\">" . _EVENT . "</span>";
					$this->month_data[$cmp['date_jour']]['event_type'][] = "divers";
				}
			}
			
			if ($nuked['birthday'] != 'off'){
                if ($nuked['birthday'] == "admin"){
					$where = " WHERE niveau > 1";
				}else{
					$where = "";
				}
				
				$query3 = "SELECT id, age, pseudo FROM " . USER_TABLE . $where;
				$data3 = mysql_query($query3);
				
				if (!$data3) echo mysql_error() . ": " . mysql_errno();
				
				while ($amp = mysql_fetch_array($data3)){
					if (!empty($amp['age'])){
						list ($jour, $mois, $an) = explode ('/', $amp['age']);
						
						if ($mois == $thismonth){
							$this->month_data[$jour]['id'][] = $amp['id'];
							$this->month_data[$jour]['event_title'][] = "<span title=\"" . _BIRTHDAY . "&nbsp;" . $amp['pseudo'] . "\">" ._BIRTHDAY . "</span>";
							$this->month_data[$jour]['event_type'][] = "birthday";
						}
					}
				}
			}
		}
		
		function print_month_name(){
			echo $this->month_name;
		}
		
		function print_year(){
			echo $this->year;
		}
		
		function print_datestring(){
			echo $this->month_name . ", " . $this->year;
		}
		
		function days_in_month($month, $year){
			$months30 = array("04", "06", "09", "11");
			
			if (in_array($month, $months30)){
				$days_in_month = 30;
			}else if ($month == "02" && $year % 4 == 0 && ($year % 100 != 0 || $year % 1000 == 0)){
				$days_in_month = 29;
			}else if ($month == "02"){
				$days_in_month = 28;
			}else{
				$days_in_month = 31;
			}
			
			return $days_in_month;
		}
		
		function _get_date_by_counter($i, $month, $year){
			$first_day = date("w" , mktime(0, 0, 0, $month, 1, $year));
			$months30 = array("04", "06", "09", "11");
			
			if (in_array($month, $months30)){
				$days_in_month = 30;
			}else if ($month == "02" && $year % 4 == 0 && ($year % 100 != 0 || $year % 1000 == 0)){
				$days_in_month = 29;
			}else if ($month == "02"){
				$days_in_month = 28;
			}else{
				$days_in_month = 31;
			}
			
			if(($i < $first_day) || ($i >= $days_in_month + $first_day)) return "&nbsp;";
			
			return ($i + 1 - $first_day);
		}
		
		function draw($draw_array = ""){
			global $bgcolor3, $bgcolor4, $bgcolor2, $bgcolor1;
			
			$bgcolor = $draw_array['bgcolor'] ? $draw_array['bgcolor'] : $bgcolor3;
			$table_width = $draw_array['table_width'] ? $draw_array['table_width'] : "100%";
			$table_height = $draw_array["table_height"] ? $draw_array['table_height'] : "100";
			$cellpadding = $draw_array['cellpadding'] ? $draw_array['cellpadding'] : "0";
			$cellspacing = $draw_array['cellspacing'] ? $draw_array['cellspacing'] : "0";
			$table_border = $draw_array['table_border'] ? $draw_array['table_border'] : "0";
			$table_top_row_align = $draw_array['top_row_align'] ? $draw_array['top_row_align'] : "left";
			$table_top_row_valign = $draw_array['top_row_valign'] ? $draw_array['top_row_valign'] : "top";
			$table_row_align = $draw_array['row_align'] ? $draw_array['row_align'] : "left";
			$table_row_valign = $draw_array['row_valign'] ? $draw_array['row_valign'] : "top";
			$table_top_row_cell_height = $draw_array['top_row_cell_height'] ? $draw_array['top_row_cell_height'] : "";
			$table_width = preg_match("`px`i", $table_width) ? (int) $table_width : $table_width . "%";
			
			$table_height = (int) $table_height;
			if ( $table_height == 0 ) $table_height = 250;
			
			if ($this->days_in_month($this->month_number, $this->year) == 28 && date("w" , mktime(0, 0, 0, 2, 1, $this->year)) == 0){
				$num_of_rows = 4;
			}elseif ($this->days_in_month($this->month_number, $this->year) == 30 && date("w" , mktime(0, 0, 0, $this->month_number, 1, $this->year)) > 5){
				$num_of_rows = 6;
			}elseif ($this->days_in_month($this->month_number, $this->year) == 31 && date("w" , mktime(0, 0, 0, $this->month_number, 1, $this->year)) > 4){
				$num_of_rows = 6;
			}else{
				$num_of_rows = 5;
			}
			
			echo "<table style=\"height: " . $table_height . "px;\" cellspacing=\"" . $cellspacing . "\" cellpadding=\"0\" width=\"" . $table_width . "\" border=\"" . $table_border . "\">\n";
			
			$dates_cell_height = ceil(($table_height - $table_top_row_cell_height) / $num_of_rows);
			
			$dates_cell_width = strpos("%" , $table_width) ? sprintf("%.3f" , (int) $table_width / 7) . "%" : ceil($table_width / 7);
			
			echo "<tr style=\"background: " . $bgcolor3 . ";\">\n"
			. "<td style=\"width: " . $dates_cell_width . ";\" align=\"" . $table_top_row_align . "\" valign=\"" . $table_top_row_valign . "\"><big><b>" . _SUNDAY . "</b></big></td>\n"
			. "<td style=\"width: " . $dates_cell_width . ";\" align=\"" . $table_top_row_align . "\" valign=\"" . $table_top_row_valign . "\"><big><b>" . _MONDAY . "</b></big></td>\n"
			. "<td style=\"width: " . $dates_cell_width . ";\" align=\"" . $table_top_row_align . "\" valign=\"" . $table_top_row_valign . "\"><big><b>" . _TUESDAY . "</b></big></td>\n"
			. "<td style=\"width: " . $dates_cell_width . ";\" align=\"" . $table_top_row_align . "\" valign=\"" . $table_top_row_valign . "\"><big><b>" . _WENESDAY . "</b></big></td>\n"
			. "<td style=\"width: " . $dates_cell_width . ";\" align=\"" . $table_top_row_align . "\" valign=\"" . $table_top_row_valign . "\"><big><b>" . _THRUSDAY . "</b></big></td>\n"
			. "<td style=\"width: " . $dates_cell_width . ";\" align=\"" . $table_top_row_align . "\" valign=\"" . $table_top_row_valign . "\"><big><b>" . _FRIDAY . "</b></big></td>\n"
			. "<td style=\"width: " . $dates_cell_width . ";\" align=\"" . $table_top_row_align . "\" valign=\"" . $table_top_row_valign . "\"><big><b>" . _SATURDAY . "</b></big></td></tr>\n";
			
			for($i = 0 ; $i < $num_of_rows * 7 ; $i++){
				
				if ($i == 0) echo "<tr>";
				if ($i % 7 == 0 && $i != 0) echo "</tr><tr>";
				$theday = $this->_get_date_by_counter($i, $this->month_number, $this->year);
				if ($this->month_data[$theday]['event_title'][0]){
					
					$CountMonthData = count($this->month_data[$theday]['event_title']);
					for($j = 0 ; $j < $CountMonthData; $j++){
						
						$color1 = $bgcolor;
						$theevent .= "&nbsp;<b><big>·</big></b>&nbsp;<a href=\"javascript:openWin('" . $this->month_data[$theday]['event_type'][$j] . "', '" . $this->month_data[$theday]['id'][$j] . "', '" . $theday . "', '" . $_REQUEST['m'] . "', '" . $_REQUEST['y'] . "')\">" . $this->month_data[$theday]['event_title'][$j] . "</a><br />";
					}
					
				}else{
					$theevent = "";
					$color1 = $bgcolor;
				}
				
				if (is_numeric($theday)){
					$dd=date(d);
					$mm=date(m);
					$yyyy=date(Y);
					if ($theday == $dd && $this->month_number == $mm && $this->year == $yyyy) $border = "border: 1px solid red;";
					else if ($theevent) $border = "border: 1px solid " . $bgcolor3 . ";";
					else $border = "";
					
					echo "<td style=\"background: " . $color1 . ";width: " . $dates_cell_width . ";height: " . $dates_cell_height. "px;" . $border . "\"  align=\"$table_row_align\" valign=\"$table_row_valign\">\n"
					. "<table width=\"100%\" cellspacing=\"0\" cellpadding=\"" . $cellpadding . "\" border=\"0\">\n"
					. "<tr><td style=\"background: " . $bgcolor1 . ";\">" . $theday . "</td></tr>\n"
					. "<tr><td style=\"background: " . $bgcolor2 . ";\">" . $theevent . "</td></tr></table></td>";
				}else{
					echo "<td style=\"background: " . $bgcolor3 . ";width: " . $dates_cell_width . ";height: " . $dates_cell_height. "px;\" align=\"$table_row_align\" valign=\"$table_row_valign\"></td>";
				}
				
				$theevent = "";
				
				if ($i == $num_of_rows * 7-1) echo "</tr>\n";
			}
			echo "</table>\n";
		}
	}
	
	js_popup();
	
	opentable();
	
	if (!$_REQUEST['y']) $_REQUEST['y'] = date("Y");
	if (!$_REQUEST['m']) $_REQUEST['m'] = date("m");
	
	if ($_REQUEST['y'] < 1971) $_REQUEST['y'] = 1971;
	if ($_REQUEST['y'] > 2038) $_REQUEST['y'] = 2038;
	
	$mymonth = new month($_REQUEST['m'], $_REQUEST['y']);
	
	echo "<br /><div style=\"text-align: center;\"><big><b>" . _CALENDARFOR . " : " . $mymonth->month_name . "&nbsp;" . $mymonth->year . "</b></big><br /><br /><small><i>" . _CLICKON . "</i></small></div><br />\n";
	
	echo "<form method=\"post\" action=\"index.php?file=Calendar\"><div style=\"text-align: center;\"><select name=\"m\">\n";
	
	$omonth = 1;
	while ($omonth < 13){
		$omonth0 = ($omonth < 10) ? "0" . $omonth : $omonth;
		$selected = ($omonth0 == $_REQUEST['m']) ? "selected=\"selected\"" : "";
		echo "<option value=\"" . $omonth0 . "\" " . $selected . ">" . $omonth0 . "</option>\n";
		$omonth++;
	}
	
	echo "</select> / <select name=\"y\">\n";
	
	$oyear = $_REQUEST['y'] -10;
	$maxyear = $_REQUEST['y'] + 10;
	while ($oyear < $maxyear){
		$selected = ($oyear == $_REQUEST['y']) ? "selected=\"selected\"" : "";
		echo "<option value=\"" . $oyear . "\" " . $selected . ">" . $oyear . "</option>\n";
		$oyear++;
	}
	
	echo "</select>&nbsp;&nbsp;<input type=\"submit\" value=\"" . _SUBMIT . "\" /></div></form>\n";
	
	echo "<table style=\"margin-left: auto;margin-right: auto;text-align: left;border: 1px solid " . $bgcolor3 . ";\" width=\"95%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n"
	. "<tr><td><table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\"><tr><td style=\"background: #FFFFFF;\">\n";
	
	$mymonth->draw(array("cellspacing" => "1" , "cellpadding" => "2" , "top_row_align" => "center" , "table_height" => "400px" , "top_row_cell_height" => 20 , "bgcolor" => "$bgcolor2" , "row_align" => "left" , "row_valign" => "top" , "font_size" => "-1"));
	
	echo "</td></tr></table></td></tr></table><div style=\"text-align: center;\"><br />\n"
	. "<input type=\"button\" onclick=\"document.location='index.php?file=Calendar&amp;m=" . $mymonth->prevmonth . "&amp;y=" . $mymonth->prevyear . "'\" value=\"" . _PREVMONTH . "\" />&nbsp;"
	. "<input type=\"button\" onclick=\"document.location='index.php?file=Calendar&amp;m=" . $mymonth->nextmonth . "&amp;y=" . $mymonth->nextyear . "'\" value=\"" . _NEXTMONTH . "\" /></div><br />\n";
	
	closetable();
}

function show_event(){
	global $bgcolor2, $user, $nuked, $theme, $language;
	
	if($_REQUEST['type'] == "birthday" && preg_match("`[a-zA-Z0-9]+$`", $_REQUEST['eid'])){
		$sql = mysql_query("SELECT pseudo, prenom, age FROM " . USER_TABLE . " WHERE id = '" . $_REQUEST['eid'] . "'");
		list($pseudo, $prenom, $birthday) = mysql_fetch_array($sql);
		
		list ($jour, $mois, $an) = explode ('/', $birthday);
		$age = $_REQUEST['y'] - $an;
		if ($_REQUEST['m'] < $mois) $age = $age - 1;
		if ($_REQUEST['d'] < $jour && $_REQUEST['m'] == $mois) $age = $age-1;
		$nom = empty($prenom) ? $pseudo : $prenom;
		
		echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">\n"
		. "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"fr\">\n"
		. "<head><title>" . _BIRTHDAY . " : " . $pseudo . "</title>\n"
		. "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\" />\n"
		. "<meta http-equiv=\"content-style-type\" content=\"text/css\" />\n"
		. "<link title=\"style\" type=\"text/css\" rel=\"stylesheet\" href=\"themes/" . $theme . "/style.css\" /></head>\n"
		. "<body style=\"background: " . $bgcolor2 . ";\">\n"
		. "<table width=\"100%\" cellpadding=\"3\" cellspacing=\"1\">\n"
		. "<tr><td align=\"center\"><big><b>" . _BIRTHDAY . " : " . $pseudo . "</b></big></td></tr><tr><td>&nbsp;</td></tr>\n"
		. "<tr><td align=\"center\">" . _BIRTHDAYTEXT . " <b>" . $nom . "</b> " . _BIRTHDAYTEXTSUITE . " <b>" . $age . "</b> " . _YEARSOLD . "</td></tr>\n"
		. "<tr><td>&nbsp;</td></tr><tr><td align=\"center\"><b><a href=\"#\" onclick=\"self.close()\">" . _CLOSEWINDOW . "</a></b></td></tr></table></body></html>";
		
	}else if (is_numeric($_REQUEST['eid'])){
		
		$sql = mysql_query("SELECT titre, description, date_jour, date_mois, date_an, heure, auteur FROM " . CALENDAR_TABLE . " WHERE id = '" . $_REQUEST['eid'] . "'");
		list($titre, $description, $jour, $mois, $an, $heure, $auteur) = mysql_fetch_array($sql);
		
		$description = icon($description);
		
		$date = ($language == "french") ? $jour . "/" . $mois . "/" . $an : $mois . "/" . $jour . "/" . $an;
		
		echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">\n"
		. "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"fr\">\n"
		. "<head><title>" . $titre. "&nbsp;" . _THE . "&nbsp;" . $date . "&nbsp;" . $heure . "</title>\n"
		. "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\" />\n"
		. "<meta http-equiv=\"content-style-type\" content=\"text/css\" />\n"
		. "<link title=\"style\" type=\"text/css\" rel=\"stylesheet\" href=\"themes/" . $theme . "/style.css\" /></head>\n"
		. "<body style=\"background: " . $bgcolor2 . ";\">\n"
		. "<table width=\"100%\" cellpadding=\"1\" cellspacing=\"0\">\n"
		. "<tr><td align=\"center\"><big><b>" . $titre . "&nbsp;" . _THE . "&nbsp;" . $jour . "/" . $mois . "/" . $an;
		
		if (!empty($heure)) echo"&nbsp;" . _AT . "&nbsp;" . $heure;
		
		echo "</b></big><br />" . _ADDEDBY . " <b>" . $auteur . "</b></td></tr><tr><td>&nbsp;</td></tr>\n"
		. "<tr><td><b>" . _DESCR . " : </b>" . $description . "</td></tr></table>\n"
		. "<div style=\"text-align: center;\"><br /><a href=\"#\" onclick=\"self.close()\"><b>" . _CLOSEWINDOW . "</b></a><br /></div></body></html>";
	}
}

function cleanList($val, $list){
	$CountList = count($list);
	for($i = 0; $i <= $CountList-1;$i++){
		$sep = ($i == 0 || ($i == 1 && $list[0] == $val)) ? "" : "|";
		if ($list[$i] != $val) $cnt .= $sep . $list[$i];
	}
	return $cnt;
}

if ($visiteur >= $level_access && $level_access > -1){
	
	compteur("Calendar");
	
	switch ($_REQUEST['op']){
		
		case "show_event":
		show_event();
		break;

        case"iminent":
		iminent($_REQUEST['eid']);
		break;

        default:
		index();
		break;
    }

}elseif ($level_access == -1){
	
    opentable();
    echo '<div style="text-align:center;padding:25px 0">'._MODULEOFF.'<br /><br /><a href="javascript:history.back()"><b>'._BACK.'</b></a></div>';
    closetable();
	
}elseif ($level_access == 1 && $visiteur == 0){
	
    opentable();
    echo '<div style="text-align:center;padding:25px 0">'._USERENTRANCE.'<br /><br /><b><a href="index.php?file=User&amp;op=login_screen">'._LOGINUSER.'</a> | <a href="index.php?file=User&amp;op=reg_screen">'._REGISTERUSER.'</a></b></div>';
    closetable();
	
}else{
	
    opentable();
    echo '<div style="text-align:center;padding:25px 0">'._NOENTRANCE.'<br /><br /><a href="javascript:history.back()"><b>'._BACK.'</b></a></div>';
    closetable();
}

?>