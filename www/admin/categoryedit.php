<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: categoryedit.php,v 1.33 2000/03/07 13:20:24 tperdue Exp $

require "pre.php";    
session_require(array('group'=>'1','admin_flags'=>'A'));

site_header(array('title'=>'Editing Category'));

// group remove
if ($HTTP_GET_VARS[form_parentrm]) {
	db_query("DELETE FROM category_link WHERE parent='$HTTP_GET_VARS[form_parentrm]' "
		. "AND child='$HTTP_GET_VARS[form_childrm]'");
}


?>

<p>Alexandria Category Edit for: <b><?php print category_getname($form_cat); ?></b>
<a href="categoryedit-rename.php?form_catrename=<?php print $form_cat; ?>">[Rename]</a>
<br><a href="categorylist.php">[Return to Category List]</a>

<p><b>Parents</b>
<a href="categoryedit-newlink.php?form_newchild=<?php print $form_cat; ?>&form_cat=<?php print $form_cat; ?>">[New Parent]</a>
<br>&nbsp;
<?php
$res_par = db_query("SELECT category.category_id AS parent_id,"
	. "category.category_name AS parent_name FROM category,category_link "
	. "WHERE category.category_id=category_link.parent AND "
	. "category_link.child=$form_cat");
while ($row_par = db_fetch_array($res_par)) {
	print "<br>$row_par[parent_name] "
         . "<A href=\"categoryedit.php?form_cat=$form_cat&form_parentrm=$row_par[parent_id]&form_childrm=$form_cat\">"
         . "[Remove Link]</A>";
}
?>
<p><b>Children</b>
<a href="categoryedit-newlink.php?form_newparent=<?php print $form_cat; ?>&form_cat=<?php print $form_cat; ?>">[New Child]</a>
<br>&nbsp;
<?php
$res_child = db_query("SELECT category.category_id AS child_id,"
	. "category.category_name AS child_name FROM category,category_link "
	. "WHERE category.category_id=category_link.child AND "
	. "category_link.parent=$form_cat");
while ($row_child = db_fetch_array($res_child)) {
	print "<br>$row_child[child_name] "
         . "<A href=\"categoryedit.php?form_cat=$form_cat&form_parentrm=$form_cat&form_childrm=$row_child[child_id]\">"
         . "[Remove Link]</A>";
}
?>

<?php
site_footer(array());
site_cleanup(array());
?>
