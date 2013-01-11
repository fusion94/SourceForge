<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: 404.php,v 1.9 2000/01/13 18:36:34 precision Exp $

require "pre.php";    // Initial db and session library, opens session
site_header(array(title=>"Requested Page not Found (Error 404)"));

if (session_issecure()) {
	echo "<a href=\"https://sourceforge.net\">";
} else {
	echo "<a href=\"http://sourceforge.net\">";
}

echo "<CENTER><H1>PAGE NOT FOUND</H1></CENTER>";

echo "<P>";

html_box1_top('Search');
menu_show_search_box();
html_box1_bottom();

echo "<P>";

site_footer(array());
site_cleanup(array());
?>
