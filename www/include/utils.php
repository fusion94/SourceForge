<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: utils.php,v 1.86 2000/01/28 16:46:59 tperdue Exp $


function util_unconvert_htmlspecialchars($string) {
	if (strlen($string) < 1) {
		return '';
	} else {
		$string=str_replace('&nbsp;',' ',$string);
		$string=str_replace('&quot;','"',$string);
		$string=str_replace('&amp;','&',$string);
		$string=str_replace('&gt;','>',$string);
		$string=str_replace('&lt;','<',$string);
		return $string;
	}
}

function util_build_select_box_from_array ($vals,$select_name,$checked_val="xzxz") {
	/*
		Takes one array, with the first array being the "id" or value
		and the array being the text you want displayed

		The second parameter is the name you want assigned to this form element

		The third parameter is optional. Pass the value of the item that should be checked
	*/

	echo '
		<SELECT NAME="'.$select_name.'">';

	$rows=count($vals);

	for ($i=0; $i<$rows; $i++) {
		echo '
			<OPTION VALUE="'.$i.'"';
		if ($i == $checked_val) {
			echo ' SELECTED';
		}
		echo '>'.$vals[$i].'</OPTION>';
	}
	echo '
		</SELECT>';

}

function util_build_select_box_from_arrays ($vals,$texts,$select_name,$checked_val="xzxz") {
	/*
		Takes two arrays, with the first array being the "id" or value
		and the other array being the text you want displayed

		The third parameter is the name you want assigned to this form element

		The fourth parameter is optional. Pass the value of the item that should be checked
	*/

	echo '
		<SELECT NAME="'.$select_name.'">';
	echo '
		<OPTION VALUE="100">None</OPTION>';

	$rows=count($vals);
	if (count($texts) != $rows) {
		echo 'ERROR - uneven row counts';
	}

	for ($i=0; $i<$rows; $i++) {
		if ($vals[$i] != '100') {
			echo '
				<OPTION VALUE="'.$vals[$i].'"';
			if ($vals[$i] == $checked_val) {
				echo ' SELECTED';
			}
			echo '>'.$texts[$i].'</OPTION>';
		}
	}
	echo '
		</SELECT>';

}

function build_select_box ($result, $name, $checked_val="xzxz") {
	/*
		Takes a result set, with the first column being the "id" or value
		and the second column being the text you want displayed

		The second parameter is the name you want assigned to this form element

		The third parameter is optional. Pass the value of the item that should be checked
	*/

	util_build_select_box_from_arrays (result_column_to_array($result,0),result_column_to_array($result,1),$name,$checked_val);
}

function build_multiple_select_box ($result,$name,$checked_array,$size='8') {
	/*
		Takes a result set, with the first column being the "id" or value
		and the second column being the text you want displayed

		The second parameter is the name you want assigned to this form element

		The third parameter is an array of checked values;

		The fourth parameter is optional. Pass the size of this box
	*/

	$checked_count=count($checked_array);
	echo '
		<SELECT NAME="'.$name.'" MULTIPLE SIZE="'.$size.'">';
	/*
		Put in the default NONE box
	*/
	echo '
		<OPTION VALUE="100"';
	for ($j=0; $j<$checked_count; $j++) {
		if ($checked_array[$j] == '100') {
			echo ' SELECTED';
		}
	}
	echo '>None</OPTION>';

	$rows=db_numrows($result);

	for ($i=0; $i<$rows; $i++) {
		if (db_result($result,$i,0) != '100') {
			echo '
				<OPTION VALUE="'.db_result($result,$i,0).'"';
			/*
				Determine if it's checked
			*/
			$val=db_result($result,$i,0);
			for ($j=0; $j<$checked_count; $j++) {
				if ($val == $checked_array[$j]) {
					echo ' SELECTED';
				}
			}
			echo '>'.$val.'-'. substr(db_result($result,$i,1),0,35). '</OPTION>';
		}
	}
	echo '
		</SELECT>';
}

function result_column_to_array($result, $col=0) {
	/*
		Takes a result set and turns the optional column into
		an array
	*/
	$rows=db_numrows($result);
	for ($i=0; $i<$rows; $i++) {
		$array[]=db_result($result,$i,$col);
	}
	return $array;
}

function util_make_links ($data='') {
	if(empty($data)) { return $data; }

	$lines = split("\n",$data);
	while ( list ($key,$line) = each ($lines)) {
		$line = eregi_replace("([ \t]|^)www\."," http://www.",$line);
		$text = eregi_replace("([a-zA-Z]+://[^ )\r\n]+)","<A href=\"\\1\" target=\"_NEW\">\\1</A>",$line);
		$text = eregi_replace("([-a-z0-9_]+(\.[_a-z0-9-]+)*@([a-z0-9-]+(\.[a-z0-9-]+)+))","<A HREF=\"mailto:\\1\">\\1</A>",$text);
		$newText .= $text;
	}
	return $newText;
}

function show_priority_colors_key() {

	echo '<P><B>Priority Colors:</B><BR>

		<TABLE BORDER=0><TR>';

	for ($i=1; $i<10; $i++) {
		echo '
			<TD BGCOLOR="'.get_priority_color($i).'">'.$i.'</TD>';
	}
	echo '</tr></table>';
}


/*
	Set up the priority color array one time only
*/
/*
$bgpri[1] = '#dadada';
$bgpri[2] = '#dad0d0';
$bgpri[3] = '#dababa';
$bgpri[4] = '#dab0b0';
$bgpri[5] = '#da9a9a';
$bgpri[6] = '#da9090';
$bgpri[7] = '#da7a7a';
$bgpri[8] = '#da7070';
$bgpri[9] = '#da5a5a';
*/
$bgpri[1] = '#dadada';
$bgpri[2] = '#dad0d0';
$bgpri[3] = '#dacaca';
$bgpri[4] = '#dac0c0';
$bgpri[5] = '#dababa';
$bgpri[6] = '#dab0b0';
$bgpri[7] = '#daaaaa';
$bgpri[8] = '#da9090';
$bgpri[9] = '#da8a8a';


function get_priority_color ($index) {
	/*
		Return the color value for the index that was passed in
	*/
	global $bgpri;
	
	return $bgpri[$index];
}

function build_priority_select_box ($name='priority', $checked_val='5') {
	/*
		Return a select box of standard priorities.
		The name of this select box is optional and so is the default checked value
	*/
	?>
	<SELECT NAME="<?php echo $name; ?>">
	<OPTION VALUE="1"<?php if ($checked_val=="1") {echo " SELECTED";} ?>>1 - Lowest</OPTION>
	<OPTION VALUE="2"<?php if ($checked_val=="2") {echo " SELECTED";} ?>>2</OPTION>
	<OPTION VALUE="3"<?php if ($checked_val=="3") {echo " SELECTED";} ?>>3</OPTION>
	<OPTION VALUE="4"<?php if ($checked_val=="4") {echo " SELECTED";} ?>>4</OPTION>
	<OPTION VALUE="5"<?php if ($checked_val=="5") {echo " SELECTED";} ?>>5 - Medium</OPTION>
	<OPTION VALUE="6"<?php if ($checked_val=="6") {echo " SELECTED";} ?>>6</OPTION>
	<OPTION VALUE="7"<?php if ($checked_val=="7") {echo " SELECTED";} ?>>7</OPTION>
	<OPTION VALUE="8"<?php if ($checked_val=="8") {echo " SELECTED";} ?>>8</OPTION>
	<OPTION VALUE="9"<?php if ($checked_val=="9") {echo " SELECTED";} ?>>9 - Highest</OPTION>
	</SELECT>
<?php

}

// ########################################### checkbox array
// ################# mostly for group languages and environments

function utils_buildcheckboxarray($options,$name,$checked_array) {
	$option_count=count($options);
	$checked_count=count($checked_array);

	for ($i=1; $i<=$option_count; $i++) {
		echo '
			<BR><INPUT type="checkbox" name="'.$name.'" value="'.$i.'"';
		for ($j=0; $j<$checked_count; $j++) {
			if ($i == $checked_array[$j]) {
				echo ' CHECKED';
			}
		}
		echo '> '.$options[$i];
	}
}

Function GraphResult($result,$title) {

/*
	GraphResult by Tim Perdue, PHPBuilder.com

	Takes a database result set.
	The first column should be the name,
	and the second column should be the values

	####
	####   Be sure to include (HTML_Graphs.php) before hitting these graphing functions
	####
*/

	/*
		db_ should be replaced with your database, aka mysql_ or pg_
	*/
	$rows=db_numrows($result);

	if ((!$result) || ($rows < 1)) {
		echo 'None Found.';
	} else {
		$names=array();
		$values=array();

		for ($j=0; $j<db_numrows($result); $j++) {
			if (db_result($result, $j, 0) != '' && db_result($result, $j, 1) != '' ) {
				$names[$j]= db_result($result, $j, 0);
				$values[$j]= db_result($result, $j, 1);
			}
		}

	/*
		This is another function detailed below
	*/
		GraphIt($names,$values,$title);
	}
}

Function GraphIt($name_string,$value_string,$title) {

	/*
		GraphIt by Tim Perdue, PHPBuilder.com
	*/
	$counter=count($name_string);

	/*
		Can choose any color you wish
	*/
	$bars=array();

	for ($i = 0; $i < $counter; $i++) {
		$bars[$i]=$GLOBALS[COLOR_LTBACK1];
	}

	$counter=count($value_string);

	/*
		Figure the max_value passed in, so scale can be determined
	*/

	$max_value=0;

	for ($i = 0; $i < $counter; $i++) {
		if ($value_string[$i] > $max_value) {
			$max_value=$value_string[$i];
		}
	}

	if ($max_value < 1) {
		$max_value=1;
	}

	/*
		I want my graphs all to be 800 pixels wide, so that is my divisor
	*/

	$scale=(400/$max_value);

	/*
		I create a wrapper table around the graph that holds the title
	*/
	echo '
		<!-- Start outer graph table -->
		<TABLE BGCOLOR="'.$GLOBALS[COLOR_MENUBARBACK].'" BORDER="0" CELLSPACING="0" CELLPADDING="2">
		<TR><TD BGCOLOR="'.$GLOBALS[COLOR_MENUBARBACK].'"><FONT COLOR="WHITE"><B>'.$title.'</TD>
		</TR><TR><TD>';

	/*
		Create an associate array to pass in. I leave most of it blank
	*/

	$vals =  array(
	'vlabel'=>'',
	'hlabel'=>'',
	'type'=>'',
	'cellpadding'=>'',
	'cellspacing'=>'0',
	'border'=>'',
	'width'=>'',
	'background'=>'',
	'vfcolor'=>'',
	'hfcolor'=>'',
	'vbgcolor'=>'',
	'hbgcolor'=>'',
	'vfstyle'=>'',
	'hfstyle'=>'',
	'noshowvals'=>'',
	'scale'=>$scale,
	'namebgcolor'=>'',
	'valuebgcolor'=>'',
	'namefcolor'=>'',
	'valuefcolor'=>'',
	'namefstyle'=>'',
	'valuefstyle'=>'',
	'doublefcolor'=>'');

	/*
		This is the actual call to the HTML_Graphs class
	*/

	html_graph($name_string,$value_string,$bars,$vals);

	echo '
		</TD></TR></TABLE>
		<!-- end outer graph table -->';
}

Function  ShowResultSet($result,$title="Untitled")  {
	/*
		Very simple, plain way to show a generic result set
		Accepts a result set and title
	*/
	if  ($result)  {
		$rows  =  db_numrows($result);
		$cols  =  db_numfields($result);

		echo '
			<TABLE BORDER="0" WIDTH="100%">';

		/*  Create the title  */
		echo '
			<TR BGCOLOR="'.$GLOBALS[COLOR_MENUBARBACK].'"><TD COLSPAN="'.$cols.'"><B><FONT COLOR="WHITE">'.$title.'</B></TD></TR>';

		/*  Create  the  headers  */
		echo '
			<tr>';
		for ($i=0; $i < $cols; $i++) {
			echo '<td><B>'.db_fieldname($result,  $i).'</TD>';
		}
		echo '</tr>';

		/*  Create the rows  */
		for ($j = 0; $j < $rows; $j++) {
			if ($j % 2 == 0) {
				$row_color=' BGCOLOR="'.$GLOBALS[COLOR_LTBACK1].'"';
			} else {
				$row_color=' BGCOLOR="#FFFFFF"';
			}
			echo '<tr'.$row_color.'>';
			for ($i = 0; $i < $cols; $i++) {
				echo '<td>'.db_result($result,  $j,  $i).'</td>';
			}
			echo '</tr>';
		}
		echo '</table>';
	} else {
		echo db_error();
	}
}

// Email Verification
function validate_email ($address) {
	return (ereg('^[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+'. '@'. '[-!#$%&\'*+\\/0-9=?A-Z^_`a-z{|}~]+\.' . '[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+$', $address));
}       
