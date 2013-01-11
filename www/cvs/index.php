<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: index.php,v 1.13 2000/01/13 18:36:34 precision Exp $

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

// ######################## anonymous CVS instructions

if ($row_grp[public]) {
	print '<P><B>Anonymous CVS Access</B>
<P>This project\'s SourceForge CVS repository can be checked out through anonymous
(pserver) CVS with the following instruction set. The module you wish
to check out must be specified as the <I>modulename</I>. When prompted
for a password for <I>anonymous</I>, simply press the Enter key.

<PRE>cvs -d:pserver:anonymous@cvs.'.$row_grp[http_domain].':/cvsroot/'.$row_grp[unix_group_name].' login
cvs -z3 -d:pserver:anonymous@cvs.'.$row_grp[http_domain].':/cvsroot/'.$row_grp[unix_group_name].' co <I>modulename</I>
</PRE>

<P>Updates from within the module\'s directory do not need the -d parameter.';
}

// ############################ developer access

print '<P><B>Developer CVS Access via SSH</B>
<P>Only project developers can access the CVS tree via this method. SSH1 must
be installed on your client machine. Substitute <I>modulename</I> and
<I>developername</I> with the proper values. Enter your site password when
prompted.

<PRE>export CVS_RSH=ssh
cvs -z3 -d<I>developername</I>@cvs.'.$row_grp[http_domain].':/cvsroot/'.$row_grp[unix_group_name].' co <I>modulename</I>
</PRE>';

// ############################## CVS Browsing

if ($row_grp[public]) {
	print '<P><B>Browse the CVS Tree</B>
<P>Browsing the CVS tree gives you a great view into the current status
of this project\'s code. You may also view the complete histories of any
file in the repository.
<UL>
<LI><A href="http://cvs.sourceforge.net/cgi-bin/cvsweb.cgi?cvsroot='
.$row_grp[unix_group_name].'"><B>Browse CVS Repository</B>';
}

site_footer(array());
site_cleanup(array());
?>
