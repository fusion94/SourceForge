<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: delay.php,v 1.5 2000/05/17 21:51:55 tperdue Exp $

require "pre.php";    
site_header(array(title=>"Documentation - The Delay"));
?>

<P><B>The 6 hour cron delay</B>

<P>Many functions on the SourceForge web site affect accounts
on other SourceForge machines. This includes all functions relating
to mail aliases, shell passwords, user additions, group member
changes, cvs repository creation, etc.

<P>Updates to these other systems happen via cron 4 times per
day, so changes made on the web site will appear to be live, but
will not take effect until the next cron update.

<P>The 'cron job' is actually several crons across several machines,
which takes several minutes to process, so the cron job counter
on the main documentation page is approximate.

<P><A href="/docs/site/">[Return to Site Documentation]</A>

<?php
site_footer(array());

?>
