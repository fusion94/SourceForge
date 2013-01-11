<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: categoryedit-newlink.php,v 1.7 2000/01/13 18:36:34 precision Exp $

require "pre.php";
session_require(array('group'=>'1'));

// ########################################################

if ($GLOBALS["form_newparent"] && $GLOBALS["form_newchild"]) {
	db_query("INSERT INTO category_link (parent,child) values ("
		. "$GLOBALS[form_newparent],$GLOBALS[form_newchild])");

	session_redirect("/admin/categoryedit.php?form_cat=$GLOBALS[form_cat]");
} 

site_header(array(title=>"Welcome to Project Alexandria"));
?>

<p>New Category Link: 
<form action="categoryedit-newlink.php" method="post">
<p>New parent:
<br><?php category_popup("form_newparent",$form_newparent); ?>
<p>New child:
<br><?php category_popup("form_newchild",$form_newchild); ?>
<input type="hidden" name="form_cat" value="<?php print $GLOBALS[form_cat]; ?>">
<br><input type="submit" name="Submit" value="Submit">
</form>

<?php
site_footer(array());
site_cleanup(array());
?>
