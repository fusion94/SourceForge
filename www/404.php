<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: 404.php,v 1.11 2000/07/12 21:01:40 tperdue Exp $

require "pre.php";    // Initial db and session library, opens session
site_header(array(title=>"Requested Page not Found (Error 404)"));

if (session_issecure()) {
	echo "<a href=\"https://$GLOBALS[sys_default_domain]\">";
} else {
	echo "<a href=\"http://$GLOBALS[sys_default_domain]\">";
}

echo "<CENTER><H1>PAGE NOT FOUND</H1></CENTER>";

echo "<P>";

html_box1_top('Search');
menu_show_search_box();
html_box1_bottom();

echo "<P>";

site_footer(array());

?>
