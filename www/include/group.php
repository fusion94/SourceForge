<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: group.php,v 1.41 2000/01/21 15:14:40 tperdue Exp $

// ############################# function group_getname()

function group_getname($group_id = 0) {
	global $GROUP_NAMES;
	if ($GROUP_NAMES["group_$group_id"]) {
		//name was fetched already and stored in array
		return $GROUP_NAMES["group_$group_id"];
	} else {
		//name wasn't fetched - get it from db and store it
		$result = db_query("SELECT group_name FROM groups WHERE group_id='$group_id'");
		if ($result && db_numrows($result) > 0) {
			$GROUP_NAMES["group_$group_id"]=db_result($result,0,"group_name");
			return $GROUP_NAMES["group_$group_id"];
		} else {
			$GROUP_NAMES["group_$group_id"]="Invalid Group Id";
			return $GROUP_NAMES["group_$group_id"];
		}
	}
}

function group_getunixname($group_id = 0) {
	$res = db_query("SELECT unix_group_name FROM groups WHERE group_id='$group_id'");
	return (db_result($res,0,"unix_group_name"));
}

// **************************** group_fullname for yahoo style trail

function category_fullname($cat_id,$highlightlast = 1,$highlightany = 1) {
	$currentcat = $cat_id;
	$fullname = "";

	while($currentcat > 1) {
		// this newtext trickery is for non highlighting last categories
		$newtext = "";
		if ((($currentcat != $cat_id) || ($highlightlast)) && $highlightany) {
		$newtext .= "<A href=\"/softwaremap/?form_cat=$currentcat\">";
		}
		$newtext .= category_getname($currentcat);
		if ((($currentcat != $cat_id) || ($highlightlast)) && $highlightany) {
		$newtext .= "</A>";
		}
		$newtext .= " :: ";	
		$fullname = $newtext . $fullname;

		$res_cat = db_query("SELECT parent FROM category_link WHERE "
			. "child=$currentcat AND primary_parent=1");
		$row_cat = db_fetch_array($res_cat);
		$currentcat = $row_cat[parent];
	}	
	
	return (substr($fullname, 0, strlen($fullname) - 4));	
}

// category tree is used by software map

function category_tree($cat_id) {
	$currentcat = $cat_id;
	$fullname = "";

	while($currentcat > 1) {
		// this newtext trickery is for non highlighting last categories
		$newtext = "";
		$newtext .= $currentcat;
		$newtext .= "::";	
		$fullname = $newtext . $fullname;
		$res_cat = db_query("SELECT parent FROM category_link WHERE "
			. "child=$currentcat AND primary_parent=1");
		$row_cat = db_fetch_array($res_cat);
		$currentcat = $row_cat[parent];
	}	
	
	return (substr($fullname, 0, strlen($fullname) - 2));	
}

function group_fullname($group_id = 0) {
	$res_cat = db_query("SELECT category_id FROM group_category WHERE "
		. "group_id=$group_id AND primary_category=1");
	$row_cat = db_fetch_array($res_cat);

	$fullname = category_fullname($row_cat[category_id]);

	$fullname .= " :: " . group_getname($group_id);
	return $fullname;	
}

// ############################# function category_popup()

function category_popup($name="form_category_popup",$selected=0) {
	print "<SELECT name=\"$name\">";
	$res = db_query("SELECT category_name,category_id "
		. "FROM category ORDER BY category_name");
	while ($row = db_fetch_array($res)) {
		print "<OPTION ";
		if ($selected == $row[category_id]) print "selected ";
		print "value=\"$row[category_id]\">$row[category_name]";
	}
	print "</SELECT>";
}

// ############################# function category_getname()

function category_getname($cat_id = 0) {
	$res = db_query("SELECT category_name FROM category WHERE category_id='$cat_id'");
	return (db_result($res,0,"category_name"));
}


?>
