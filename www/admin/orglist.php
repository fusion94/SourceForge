<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: orglist.php,v 1.7 2000/03/07 13:20:24 tperdue Exp $

require "pre.php";    
session_require(array('group'=>'1','admin_flags'=>'A'));

site_header(array('title'=>"Organization List"));

// start from root if root not passed in
if (!$GLOBALS['form_orgroot']) {
	$GLOBALS['form_catroot'] = 1;
}

print "<p>SourceForge Organization List\n";
print "<br><a href=\"orgedit-add.php\">[New Organization]\n";

$res = db_query("SELECT org_name,organization_id,org_type "
		. "FROM organization ORDER BY org_name");
?>

<P>
<TABLE width=100% border=1>
<TR>
<TD><b>Organization Name (click to edit)</b></TD>
<TD><b>Type</b></TD>
<TD><b>Groups</b></TD>
<TD><b>Users</b></TD>
</TR>

<?php
while ($org = db_fetch_array($res)) {
	print "<tr>";
	print "<td><a href=\"orgedit.php?form_cat=$org[organization_id]\">$org[org_name]</a></td>";

	print "<td>$org[org_type]</td>";
	
	// parents
	$count = db_query("SELECT organization_id FROM organization_group WHERE "
		. "organization_id=$org[organization_id]");
	print ("<td>" . db_numrows($count) . "</td>");
	
	// children
	$count = db_query("SELECT organization_id FROM organization_user WHERE "
		. "organization_id=$org[organization_id]");
	print ("<td>" . db_numrows($count) . "</td>");
	
	print "</tr>\n";
}
?>

</TABLE>

<?php
site_footer(array());
site_cleanup(array());
?>
