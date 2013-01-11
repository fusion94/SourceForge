<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: index.php,v 1.94 2000/07/12 21:01:41 tperdue Exp $

require ('pre.php');    
require ($DOCUMENT_ROOT.'/project/admin/project_admin_utils.php');
require ('account.php');

// get current information
$res_grp = group_get_result($group_id);

if (db_numrows($res_grp) < 1) {
	exit_error("Invalid Group","That group could not be found.");
}

//if the project isn't active, require you to be a member of the super-admin group
if (!(db_result($res_grp,0,'status') == 'A')) {
	session_require (array('group'=>1));
}

//must be a project admin
session_require(array('group'=>$group_id,'admin_flags'=>'A'));

if ($func) {
	/*
		updating the database
	*/
	if ($func=='adduser') {
		/*
			add user to this project
		*/
		account_add_user_to_group ($group_id,$form_unix_name);
	} else if ($func=='rmuser') {
		/*
			remove a user from this portal
		*/
		$feedback .= ' Removed a User ';
		db_query("DELETE FROM user_group WHERE group_id='$group_id' AND user_id='$rm_id' AND admin_flags <> 'A'");
	}

}

project_admin_header(array('title'=>"Project Admin: ".group_getname($group_id),'group'=>$group_id));

/*
	Show top box listing trove and other info
*/

echo '<TABLE width=100% cellpadding=2 cellspacing=2 border=0>
<TR valign=top><TD width=50%>';

html_box1_top("Group Edit: " . group_getname($group_id)); 

print '&nbsp;
<BR>
Short Description: '. db_result($res_grp,0,'short_description') .'
<P>
Homepage Link: <B>'. db_result($res_grp,0,'homepage') .'</B>
<P align=center>
<A HREF="http://'.$GLOBALS['sys_cvs_host'].'/cvstarballs/'. db_result($res_grp,0,'unix_group_name') .'-cvsroot.tar.gz">[ Download Your Nightly CVS Tree Tarball ]</A>
<P>
<B>Trove Categorization Info</B> - This group is in the following Trove categories:

<UL>';

// list all trove categories
$res_trovecat = db_query('SELECT trove_cat.fullpath AS fullpath,'
	.'trove_cat.trove_cat_id AS trove_cat_id '
	.'FROM trove_cat,trove_group_link WHERE trove_cat.trove_cat_id='
	.'trove_group_link.trove_cat_id AND trove_group_link.group_id='.$group_id
	.' ORDER BY trove_cat.fullpath');
while ($row_trovecat = db_fetch_array($res_trovecat)) {
	print ('<LI>'.$row_trovecat['fullpath'].' '
		.help_button('trove_cat',$row_trovecat['trove_cat_id'])."\n");
}

print '
</UL>
<P align="center">
<A href="/project/admin/group_trove.php?group_id='.$group_id.'">'
.'<B>[Edit Trove Categorization]</B></A>
';

html_box1_bottom(); 

echo '
</TD><TD>&nbsp;</TD><TD width=50%>';


html_box1_top("Group Members");

/*

	Show the members of this project

*/

$res_memb = db_query("SELECT user.realname,user.user_id ".
		"FROM user,user_group ".
		"WHERE user.user_id=user_group.user_id ".
		"AND user_group.group_id=$group_id");

	while ($row_memb=db_fetch_array($res_memb)) {
		print '
		<FORM ACTION="'. $PHP_SELF .'" METHOD="POST"><INPUT TYPE="HIDDEN" NAME="func" VALUE="rmuser">'.
		'<INPUT TYPE="HIDDEN" NAME="rm_id" VALUE="'.$row_memb['user_id'].'">'.
		'<INPUT TYPE="HIDDEN" NAME="group_id" VALUE="'. $group_id .'">'.
		'<TR><TD ALIGN="MIDDLE"><INPUT TYPE="IMAGE" NAME="DELETE" SRC="/images/ic/trash.png" HEIGHT="16" WIDTH="16" BORDER="0"></TD></FORM>'.
		'<TD><A href="/developer/index.php?form_dev='.$row_memb['user_id'].'">'.$row_memb['realname'].'</A></TD></TR>';
	}

/*
	Add member form
*/

echo '
	<FORM ACTION="'. $PHP_SELF .'" METHOD="POST">
	<INPUT TYPE="hidden" NAME="func" VALUE="adduser">
	<INPUT TYPE="HIDDEN" NAME="group_id" VALUE="'. $group_id .'">
	<TR><TD><B>Unix Name:</B></TD><TD><INPUT TYPE="TEXT" NAME="form_unix_name" VALUE=""></TD></TR>
	<TR><TD COLSPAN="2" ALIGN="CENTER"><INPUT TYPE="SUBMIT" NAME="SUBMIT" VALUE="Add User"></TD></TR></FORM>

	<TR><TD colspan="2" align="center">
        <BR><A href="/project/admin/userperms.php?group_id='. $group_id.'">[Edit Member Permissions]</A></TD></TR>
';
 
html_box1_bottom();


echo '</TD></TR>

<TR valign=top><TD width=50%>';

/*
	Tool admin pages
*/

html_box1_top('Tool Admin');

echo '
<BR>
<A HREF="/docman/admin/?group_id='.$group_id.'">DocManager Admin</A><BR>
<A HREF="/bugs/admin/?group_id='.$group_id.'">Bug Admin</A><BR>
<A HREF="/patch/admin/?group_id='.$group_id.'">Patch Admin</A><BR>
<A HREF="/mail/admin/?group_id='.$group_id.'">Mail Admin</A><BR>
<A HREF="/news/admin/?group_id='.$group_id.'">News Admin</A><BR>
<A HREF="/pm/admin/?group_id='.$group_id.'">Task Manager Admin</A><BR>
<A HREF="/support/admin/?group_id='.$group_id.'">Support Manager Admin</A><BR>
<A HREF="/forum/admin/?group_id='.$group_id.'">Forum Admin</A><BR>
';

html_box1_bottom(); 




echo '</TD>

<TD>&nbsp;</TD>

<TD width=50%>';

/*
	Show filerelease info
*/

html_box1_top("File Releases"); ?>
	&nbsp;<BR>
	<CENTER>
	<A href="filerelease-list.php?group_id=<?php print $group_id; ?>"><B>[Edit File Releases]</B></A>
	<BR><A href="addfile.php?group_id=<?php print $group_id; ?>">[Release New File]</A>
	</CENTER>

	<HR>
	<B>Modules:</B> <A href="/docs/site/modules.php">Documentation</A> (Very Important!)

	<BR>&nbsp;<?php
	$res_module = db_query("SELECT * FROM filemodule WHERE group_id=$group_id");
	?>
	<TABLE width=100% cellspacing=0 cellpadding=0 border=0>
		<TR><TD><B>Name</B></TD><TD><B>Release</B></TD><TD>&nbsp;</TD></TR><?php
	while ($row_module = db_fetch_array($res_module)) {
		print "<TR><TD>$row_module[module_name]</TD>";
		print "<TD>$row_module[recent_filerelease]</TD>";
		print "<TD><A href=\"module-edit.php?group_id=$group_id&"
			. "form_filemodule_id=$row_module[filemodule_id]\">[Edit]</A></TD></TR>";
	}
	?>
	</TD></TR></TABLE>
	<P align=center><A href="module-add.php?group_id=<?php print $group_id; ?>">[Define New Module]</A>

	<?php html_box1_bottom(); ?>
</TD>
</TR>
</TABLE>

<?php
project_admin_footer(array());

?>
