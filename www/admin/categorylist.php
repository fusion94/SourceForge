<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: categorylist.php,v 1.8 2000/05/17 21:51:55 tperdue Exp $

require "pre.php";    
session_require(array('group'=>'1','admin_flags'=>'A'));

site_header(array('title'=>'Alexandria: Category List'));

// start from root if root not passed in
if (!$GLOBALS['form_catroot']) {
	$GLOBALS['form_catroot'] = 1;
}
print "<p>Alexandria Category List\n";
print "<br><a href=\"categoryedit-add.php\">[New Category]\n";

$res = db_query("SELECT category_name,category_id "
		. "FROM category ORDER BY category_name");
?>

<P>
<TABLE width=100% border=1>
<TR>
<TD><b>Category Name (click to edit)</b></TD>
<TD><b>Root?</b></TD>
<TD><b>Parents</b></TD>
<TD><b>Children</b></TD>
</TR>

<?php
while ($cat = db_fetch_array($res)) {
	print "<tr>";
	print "<td><a href=\"categoryedit.php?form_cat=$cat[category_id]\">$cat[category_name]</a></td>";
	
	// root?
	$count = db_query("SELECT category_link_id FROM category_link WHERE parent=1 "
		. "AND child=$cat[category_id]");
	if (db_numrows($count)) {
		print ("<td>Yes</td>");
	} else {
		print ("<td>No</td>");
	}
	
	// parents
	$count = db_query("SELECT category_link_id FROM category_link WHERE "
		. "child=$cat[category_id]");
	print ("<td>" . db_numrows($count) . "</td>");
	
	// children
	$count = db_query("SELECT category_link_id FROM category_link WHERE "
		. "parent=$cat[category_id]");
	print ("<td>" . db_numrows($count) . "</td>");
	
	print "</tr>\n";
}
?>

</TABLE>

<?php
site_footer(array());

?>
