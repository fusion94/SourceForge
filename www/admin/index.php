<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: index.php,v 1.43 2000/01/13 18:36:34 precision Exp $

require "pre.php";
session_require(array('group'=>'1'));

site_header(array(title=>"Alexandria Admin"));

$abc_array = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','0','1','2','3','4','5','6','7','8','9');

?>

<p>Administrative Functions
<p><i><b>Warning!</b> These functions currently have minimal error checking,
if any. They are fine to play with but may not act as expected if you leave
fields blank, etc... Also, navigating the admin functions with the 
<b>back</b> button is highly unadvised.</i>

<p><B>User/Group/Category Maintenance</B>
<ul>
<li><a href="userlist.php">Display Full User List/Edit Users</a>&nbsp;&nbsp;

<li>Display Users Beginning with : 
<?php
	for ($i=0; $i < count($abc_array); $i++) {
		echo "<a href=\"userlist.php?user_name_search=$abc_array[$i]\">$abc_array[$i]</a>|";
	}
?>
<BR>&nbsp;
<li><a href="grouplist.php">Display Full Group List/Edit Groups</a>

<li>Display Groups Beginning with : 
<?php
	for ($i=0; $i < count($abc_array); $i++) {
		echo "<a href=\"grouplist.php?group_name_search=$abc_array[$i]\">$abc_array[$i]</a>|";
	}
?>
<LI>Groups in <a href="grouplist.php?status=I"><B>I</B> Status</A>
<LI>Groups in <a href="grouplist.php?status=P"><B>P</B> Status</A>
<LI>Groups in <a href="grouplist.php?status=D"><B>D</B> Status</A>
<BR>&nbsp;
<li><a href="categorylist.php">Display Full Category List/Edit Categories</a>
<BR>&nbsp;
<li><a href="orglist.php">Display Full Organization List/Edit Organizations</a>
</ul>

<P><B>Statistics</B>
<ul>
<li><a href="lastlogins.php">View Most Recent Logins</A>
</ul>

<P><B>Site Utilities</B>

<P><B>Site Stats</B>
<?php
        db_query("SELECT count(*) AS count FROM user WHERE status='A'");
        $row = db_fetch_array();
        print "<P>Registered active site users: <B>$row[count]</B>";

        db_query("SELECT count(*) AS count FROM groups");
        $row = db_fetch_array();
        print "<BR>Registered projects: <B>$row[count]</B>";

        db_query("SELECT count(*) AS count FROM groups WHERE status='A'");
        $row = db_fetch_array();
        print "<BR>Registered/hosted projects: <B>$row[count]</B>";

        db_query("SELECT count(*) AS count FROM groups WHERE status='P'");
        $row = db_fetch_array();
	print "<BR>Pending projects: <B>$row[count]</B>";
?>


<?php
site_footer(array());
site_cleanup(array());
?>
