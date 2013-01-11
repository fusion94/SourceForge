<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: module-add.php,v 1.10 2000/01/26 10:44:32 tperdue Exp $

require "pre.php";    
require ($DOCUMENT_ROOT.'/project/admin/project_admin_utils.php');

session_require(array('group'=>$group_id,'admin_flags'=>'A'));

if ($GLOBALS[Submit] && $form_newmodule) {
	// unix account if not one
	db_query("INSERT INTO filemodule (group_id,module_name) VALUES ($group_id,'$form_newmodule')");
	session_redirect ("/project/admin/?group_id=$group_id");
}

project_admin_header(array('title'=>'Add File Module','group'=>$group_id));
?>
<P>Add File Module to Project: <B><?php html_a_group($group_id); ?></B>

<P>Please note that a default module has already been created for you,
and this modulename can be edited.

<P>You already have a module defined for this project;
<B>are you sure you want to define another one?</B> Defining an additional
module rather than starting a new project ties this module to the
same cvs tree, web site, message forums, bug tracking, developer set,
and site references as your other modules. If this could <B>potentially</B>
become another project, go ahead and start one now.
Please read the <A href="/docs/site/modules.php">modules documentation</A>
carefully before proceeding.

<P><FORM action="module-add.php" method="post">
New Module Name:
<BR><INPUT type="text" name="form_newmodule">
<INPUT type="hidden" name="group_id" value="<?php print $group_id; ?>">
<BR><INPUT type="submit" name="Submit" value="Submit">
</FORM>

<?php
project_admin_footer(array());
?>
