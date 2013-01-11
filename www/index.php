<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: index.php,v 1.174 2000/01/29 18:14:18 tperdue Exp $

require ('pre.php');    // Initial db and session library, opens session
require ('cache.php');
require($DOCUMENT_ROOT.'/forum/forum_utils.php');

//generic_header(array('title'=>'Welcome'));
site_header(array('title'=>'Welcome'));
?>
&nbsp;
<BR>

<!-- whole page table -->
<TABLE width=100% cellpadding=0 cellspacing=0 border=0>
<TR><TD width="65%" VALIGN="TOP">

<?php
echo news_show_latest(); ?>

<B>Help and discussion</B>
<UL>
<LI><A href="/forum/forum.php?forum_id=3"><B>SourceForge Help Forum</B></A>
<LI><A href="/forum/forum.php?forum_id=2"><B>SourceForge Open Discussion Forum</B></A>
<LI><A href="/forum/forum.php?forum_id=4"><B>SourceForge Feature Request Forum</B></A>
<LI><A href="/bugs/?group_id=1"><B>SourceForge Bug Tracker</B></A> Submit a bug report.
<LI><A href="/patch/?group_id=1"><B>SourceForge Patch Manager</B></A> Submit code contributions through the web.
<LI>Join us on IRC at <B>#sourceforge</B> on <B>irc.linux.com</B> (or the OpenProjects network)
</UL>
<P>
<A HREF="/snippet/"><B><IMG SRC="/images/ic/scissors.png" HEIGHT=24 WIDTH=24  BORDER=0> 
<FONT COLOR="RED">New</FONT> Code Snippet Library</B></A> Share your scripts, 
README's, and code libraries with the community. 
Create packages of scripts with a web interface. The system even allows you 
to create new versions of scripts and packages if you change someone's code 
and wish to share it.
<P>
SourceForge is a <B>free service to 
<A href="http://www.opensource.org">Open Source</A> developers</B> offering
easy access to the best in CVS, mailing lists, bug tracking, message boards/forums,
task management, site hosting, permanent file archival, full backups,
and total web-based administration. 
<UL>
<LI>Read <A href="/docs/site/services.php">a description of the complete SourceForge package</A>,
available free to opensource developers.
</UL>

<P>
<B>Who are we? What are we doing? Why are we doing it?</B>
<P>There is too much information about this project to fit in this
introductory page. You should really take the time to visit
our <A href="/docs/site/faq.php"><B>Frequently Asked
Questions</B></A>.

<P>
<B>Site Feedback and Participation</B>
<P>
In order to get the most out of SourceForge, you'll need
to <A href="/account/register.php">register as a 
site user</A>. This will allow you to participate fully in all we have to
offer. You may of course browse the site without registering, but will
not have access to participate fully.
<P>
<B>Set Up Your Own Project</B>
<P>
<A href="/account/register.php">Register as a site user</A>, 
then <A HREF="/account/login.php">Login</A> and finally,
<A HREF="/register/">Register Your Project.</A>
<P>
Thanks... and enjoy the site.

</TD>
<TD>&nbsp;</TD>

<?php

echo '<TD width="35%" VALIGN="TOP">';

echo cache_display("show_features_boxes","show_features_boxes()",1800);


?>

</TR></TABLE>

<?php

site_footer(array());

site_cleanup(array());
?>
