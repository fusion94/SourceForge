<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: index.php,v 1.69 2000/01/13 18:36:36 precision Exp $

require "pre.php";    

session_require(array('group'=>$group_id,'admin_flags'=>'A'));

// check for changes

if ($GLOBALS[Update]) {
	db_query("UPDATE groups SET short_description='$form_shortdesc' "
		. "WHERE group_id=$group_id");
}

// get current information
$res_grp = db_query("SELECT * FROM groups WHERE group_id=$group_id");
if (db_numrows($res_grp) < 1) exit_error("Invalid Group","That group could not be found.");
$row_grp = db_fetch_array($res_grp);
if (!(($row_grp[status] == 'A') || ($row_grp[status] == 'H'))) session_require (array(group=>1));

site_header(array(title=>"Editing Project",group=>$group_id));
?>                     

<?php html_box1_top("Group Edit: " . group_getname($group_id)); 
print '&nbsp;<BR>Short Description: '.$row_grp[short_description].'
<P>Homepage Link: <B>'.$row_grp[homepage].'</B>
<P align=center>
<A href="/project/admin/editgroupinfo.php?group_id='.$group_id.'">'
.'<B>[Edit Group Public Information and Categorization]</B></A>
';
html_box1_bottom(); 
?>

<TABLE width=100% cellpadding=0 cellspacing=0 border=0>
<TR valign=top>
<TD width=50%>
<?php html_box1_top("Group Members"); ?>
&nbsp;</TR>
<?php $res_memb = db_query("SELECT user.user_name AS user_name,user.user_id AS user_id FROM user,user_group WHERE "
	. "user.user_id=user_group.user_id AND user_group.group_id=$group_id");

while ($row_memb=db_fetch_array($res_memb)) {
	print "<TR><TD><A href=\"/developer/index.php?form_dev=$row_memb[user_id]\">$row_memb[user_name]</A>";
	print "</TD><TD><A href=\"group-rmmember.php?form_user=$row_memb[user_id]&group_id=$group_id\">"
		. "[Remove from Group]</A></TD></TR>";
}
?>
<TR><TD colspan="2" align="center">&nbsp;<BR>
<A href="group-addmember.php?group_id=<?php print $group_id; ?>">[Add Group Member]</A>
<BR><A href="userperms.php?group_id=<?php print $group_id; ?>">[Edit Member Permissions]</A>
<?php html_box1_bottom(); ?>
</TD>
<TD>&nbsp;</TD>
<TD width=50%>
<?php html_box1_top("File Releases"); ?>
&nbsp;<BR>
<CENTER>
<A href="filerelease-list.php?group_id=<?php print $group_id; ?>"><B>[Edit File Releases]</B></A>
<BR><A href="addfile.php?group_id=<?php print $group_id; ?>">[Release New File]</A>
</CENTER>

<HR>
<B>Modules:</B> <A href="/docs/site/modules.php">Documentation</A> (Very Important!)

<BR>&nbsp;<?php
	$res_module = db_query("SELECT * FROM filemodule WHERE group_id=$group_id");
	?><TABLE width=100% cellspacing=0 cellpadding=0 border=0>
	<TR><TD><B>Name</B></TD><TD><B>Release</B></TD><TD>&nbsp;</TD></TR><?php
	while ($row_module = db_fetch_array($res_module)) {
		print "<TR><TD>$row_module[module_name]</TD>";
		print "<TD>$row_module[recent_filerelease]</TD>";
		print "<TD><A href=\"module-edit.php?group_id=$group_id&"
			. "form_filemodule_id=$row_module[filemodule_id]\">[Edit]</A></TD></TR>";
	} 	
	print "</TD></TR></TABLE>";
?>
<P align=center><A href="module-add.php?group_id=<?php print $group_id; ?>">[Define New Module]</A>

<?php html_box1_bottom(); ?>

</TD>
</TR>
</TABLE>

<?php
site_footer($menuarray);
site_cleanup(array());
?>
