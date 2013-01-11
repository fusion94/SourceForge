<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: myprojectlist.php,v 1.7 2000/05/17 21:51:43 tperdue Exp $

require "pre.php";    
site_header(array(title=>"My Project Listing"));

$res_proj = db_query("SELECT groups.group_name AS group_name,"
		. "groups.group_id AS group_id,"
		. "groups.status AS status,"
		. "user_group.admin_flags AS admin_flags "
		. "FROM groups,user_group WHERE "
		. "groups.group_id=user_group.group_id AND "
		. "user_group.user_id=" . user_getid());
?>

<P>Group List for: <B><?php print user_getname(); ?></B>

<P>
<TABLE width=100% cellpadding=0 cellspacing=0 border=0>
<?php
while ($row_proj = db_fetch_array($res_proj)) {
	print "<TR>\n";
	print "<TD><A href=\"/project/?group_id=$row_proj[group_id]\">$row_proj[group_name]</A></TD>";
	print "</TR>\n";
}
?>
</TABLE>

<?php
site_footer(array());

?>
