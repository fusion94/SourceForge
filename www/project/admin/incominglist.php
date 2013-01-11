<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: incominglist.php,v 1.6 2000/01/13 18:36:36 precision Exp $

require "pre.php";   
require "paths.php"; 
?>
<HTML>
<TITLE>SourceForge Incoming File List</TITLE>
<BODY bgcolor=#FFFFFF>
<P>
<?php
$dirhandle = opendir($FTPINCOMING_DIR);
while ($file = readdir($dirhandle)) {
	print "<BR>$file\n";
}
?>
</BODY>
</HTML>
<?php
site_cleanup(array());
?>
