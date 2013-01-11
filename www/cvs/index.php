<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: index.php,v 1.24 2000/02/09 09:31:38 dtype Exp $

require "pre.php";    
site_header(array(title=>"CVS Repository"));
html_tabs('cvs',$group_id);

$res_grp = db_query("SELECT * FROM groups WHERE group_id=$group_id");
if (db_numrows($res_grp) < 1) {
	print "<P><B>Invalid Group Number</B>";
	site_footer(array());
	site_cleanup(array());
	exit;
}
$row_grp = db_fetch_array($res_grp);

// ######################## table for summary info

print '<TABLE width="100%"><TR valign="top"><TD width="65%">'."\n";

// ######################## anonymous CVS instructions

if ($row_grp[public]) {
	print '<P><B>Anonymous CVS Access</B>
<P>This project\'s SourceForge CVS repository can be checked out through anonymous
(pserver) CVS with the following instruction set. The module you wish
to check out must be specified as the <I>modulename</I>. When prompted
for a password for <I>anonymous</I>, simply press the Enter key.

<P><FONT size="-1" face="courier">cvs -d:pserver:anonymous@cvs.'.$row_grp[http_domain].':/cvsroot/'.$row_grp[unix_group_name].' login
<BR>&nbsp;<BR>cvs -z3 -d:pserver:anonymous@cvs.'.$row_grp[http_domain].':/cvsroot/'.$row_grp[unix_group_name].' co <I>modulename</I>
</FONT>

<P>Updates from within the module\'s directory do not need the -d parameter.';
}

// ############################ developer access

print '<P><B>Developer CVS Access via SSH</B>
<P>Only project developers can access the CVS tree via this method. SSH1 must
be installed on your client machine. Substitute <I>modulename</I> and
<I>developername</I> with the proper values. Enter your site password when
prompted.

<P><FONT size="-1" face="courier">export CVS_RSH=ssh
<BR>&nbsp;<BR>cvs -z3 -d<I>developername</I>@cvs.'.$row_grp[http_domain].':/cvsroot/'.$row_grp[unix_group_name].' co <I>modulename</I>
</FONT>';

// ################## summary info

print '</TD><TD width="35%">';
print html_box1_top("Repository History");

// ################ is there commit info?

$res_cvshist = db_query("SELECT * FROM group_cvs_history WHERE group_id='$group_id'");
if (db_numrows($res_cvshist) < 1) {
	print '<P>This project has no CVS history.';
} else {

print '<P><B>Developer (30 day/Commits) (30 day/Adds)</B><BR>&nbsp;';

while ($row_cvshist = db_fetch_array($res_cvshist)) {
	print '<BR>'.$row_cvshist['user_name'].' ('.$row_cvshist['cvs_commits_wk'].'/'
		.$row_cvshist['cvs_commits'].') ('.$row_cvshist['cvs_adds_wk'].'/'
		.$row_cvshist['cvs_adds_wk'].')';
}

} // ### else no cvs history

// ############################## CVS Browsing

if ($row_grp[public]) {
	print '<HR><B>Browse the CVS Tree</B>
<P>Browsing the CVS tree gives you a great view into the current status
of this project\'s code. You may also view the complete histories of any
file in the repository.
<UL>
<LI><A href="http://cvs.sourceforge.net/cgi-bin/cvsweb.cgi?cvsroot='
.$row_grp[unix_group_name].'"><B>Browse CVS Repository</B>';
}

print html_box1_bottom();
print '</TD></TR></TABLE>';

site_footer(array());
site_cleanup(array());
?>
