<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: groupcategory.php,v 1.27 2000/03/07 13:20:24 tperdue Exp $

require "pre.php";    
session_require(array('group'=>'1','admin_flags'=>'A'));

site_header(array('title'=>"Alexandria: Admin: Group Info"));

// group remove
if ($HTTP_GET_VARS['form_grouprm']) {
	db_query("DELETE FROM group_category WHERE group_id='$HTTP_GET_VARS[form_grouprm]' "
		. "AND category_id='$HTTP_GET_VARS[form_catremove]'");
}

if ($HTTP_POST_VARS['form_category_popup']) {
	db_query("INSERT INTO group_category (group_id,category_id) VALUES "
		. "($HTTP_POST_VARS[form_group],$HTTP_POST_VARS[form_category_popup])");
}

?>
<p>Alexandria Group Category Edit for group: 
<b><?php print $form_group . ": " . group_getname($form_group); ?></b>
<?php if ($submit_error) print "<p>$submit_error"; ?>
<form action="groupcategory.php" method="post">
<input name="form_group" type="hidden" value="<?php print $form_group; ?>">
<p>Current Categories:<br>&nbsp;
<?php
// now get listing of categories for that group
$res_cat = db_query("SELECT category.category_name AS category_name, "
	. "category.category_id AS category_id FROM "
	. "category,group_category WHERE group_category.group_id=$GLOBALS[form_group] AND "
	. "category.category_id=group_category.category_id");
while ($row_cat = db_fetch_array($res_cat)) {
	print ("<br><b>" . category_fullname($row_cat['category_id']) . "</b> "
		. "<a href=\"groupcategory.php?form_group=$form_group&form_grouprm=$form_group&form_catremove=$row_cat[category_id]\">"
		. "[Remove Group from Category]</a>");
}
?>
<hr>
<p>Add Group to Category:
<br><?php category_popup(); ?>
<input type="hidden" name="form_group" value="<?php print $form_group; ?>">
<p><input type="submit" name="Submit" value="Submit">
</form>

<?php
site_footer(array());
site_cleanup(array());
?>
