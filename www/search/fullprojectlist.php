<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: fullprojectlist.php,v 1.4 2000/01/13 18:36:36 precision Exp $

require "pre.php";    
site_header(array(title=>"Full Project Listing"));

print '<P><B>SourceForge Full Project Listing</B>
<BR><I>All actively hosted projects are displayed.</I>
';

$res_grp = db_query('SELECT group_id,group_name,short_description FROM groups '
	.'WHERE status=\'A\' AND public=1 ORDER BY group_name');

while ($row_grp = db_fetch_array($res_grp)) {
	// is this the first of a new letter?
	if (!$FIRSTLETTER[strtoupper(substr($row_grp[group_name],0,1))]) {
		$FIRSTLETTER[strtoupper(substr($row_grp[group_name],0,1))] = 1;
		print '<P><FONT size="+1"><B>'
			.strtoupper(substr($row_grp[group_name],0,1))
			.'</B></FONT>'."\n";
	}

	print '<LI><A href="/project/?group_id='.$row_grp[group_id].'">'
		.$row_grp[group_name].'</A>
<BR><FONT size="-1">'.$row_grp[short_description].'</FONT>
';
}

site_footer(array());
site_cleanup(array());
?>
