<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: categoryedit-add.php,v 1.8 2000/03/07 13:20:24 tperdue Exp $

require "pre.php";
session_require(array('group'=>'1','admin_flags'=>'A'));

// ########################################################

if ($GLOBALS["Submit"]) {
	if ($GLOBALS[form_catname]) {
	db_query("INSERT INTO category (category_name) values ('"
		. $GLOBALS[form_catname] . "')");
	} 
	session_redirect("/admin/categorylist.php");
} 

site_header(array(title=>"Welcome to Project Alexandria"));
?>

<form action="categoryedit-add.php" method="post">
<p>New category name:
<br><input type="text" name="form_catname">
<br><input type="submit" name="Submit" value="Submit">
</form>

<?php
site_footer(array());
site_cleanup(array());
?>