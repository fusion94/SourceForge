<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: groupedit-newcat.php,v 1.7 2000/03/07 13:20:24 tperdue Exp $

require "pre.php";
session_require(array('group'=>'1','admin_flags'=>'A'));

// ########################################################

if ($GLOBALS["form_newcat"]) {
	db_query("INSERT INTO group_category (group_id,category_id) values ("
		. "$GLOBALS[group_id],$GLOBALS[form_newcat])");

	session_redirect("/admin/groupedit.php?group_id=$GLOBALS[group_id]");
} 

site_header(array('title'=>"Welcome to Project Alexandria"));
?>

<form action="groupedit-newcat.php" method="post">
<p>New Category Link: 
<br><?php category_popup("form_newcat",$form_newcat); ?>
<input type="hidden" name="group_id" value="<?php print $GLOBALS['group_id']; ?>">
<br><input type="submit" name="Submit" value="Submit">
</form>

<?php
site_footer(array());
site_cleanup(array());
?>
