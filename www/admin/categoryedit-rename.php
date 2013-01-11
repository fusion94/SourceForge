<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: categoryedit-rename.php,v 1.10 2000/03/07 13:20:24 tperdue Exp $

require "pre.php";
session_require(array('group'=>'1','admin_flags'=>'A'));

// ########################################################

if ($GLOBALS["Submit"]) {
	if ($GLOBALS[form_cat_newname]) {
		db_query("UPDATE category SET category_name='$GLOBALS[form_cat_newname]' WHERE "
			. "category_id=$GLOBALS[form_catrename]");
	}
	session_redirect("/admin/categoryedit.php?form_cat=$form_catrename");
} 

site_header(array(title=>"Welcome to Project Alexandria"));
?>

<p>Renaming Category: 
<b><?php print $GLOBALS[form_catrename] . ": " . category_getname($GLOBALS[form_catrename]); ?></b>
<form action="categoryedit-rename.php" method="post">
<p>New category name:
<br><input type="text" name="form_cat_newname">
<input type="hidden" name="form_catrename" value="<?php print $GLOBALS[form_catrename]; ?>">
<br><input type="submit" name="Submit" value="Submit">
</form>

<?php
site_footer(array());
site_cleanup(array());
?>
