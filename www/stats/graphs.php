<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: graphs.php,v 1.4 2000/08/31 23:15:04 msnelham Exp $ 
require('pre.php');
require('site_stats_utils.php');

$HTML->header(array(title=>"SourceForge Site Statistics "));

   // require you to be a member of the super-admin group
session_require(array('group'=>'1','admin_flags'=>'A'));


//
// BEGIN PAGE CONTENT CODE
//

echo "\n\n";

print '<DIV ALIGN="CENTER">' . "\n";
print '<font size="+1"><b>Sitewide Statistics Graphs</b></font><BR>' . "\n";
?>

<HR>

<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr>
<td align="center"><a href="index.php">OVERVIEW STATS</a></td>
<td align="center"><a href="projects.php">PROJECT STATS</a></td>
<td align="center"><B>SITE GRAPHS</B></td>
</tr>
</table>

<HR>

<?php

print '<BR><BR>' . "\n";
print '<IMG SRC="views_graph.png">' . "\n";
print '<BR><BR>' . "\n";
print '<IMG SRC="users_graph.png">' . "\n";
print '<BR><BR>' . "\n";
print '</DIV>' . "\n";

//
// END PAGE CONTENT CODE
//

$HTML->footer( array() );
?>
