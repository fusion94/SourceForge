<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: index.php,v 1.5 2000/01/30 09:55:56 precision Exp $

require "pre.php";    
site_header(array(title=>"Top Project Listings"));
?>

<P><B>Top SourceForge Projects</B></P>

<P>We track many project usage statistics on SourceForge, and display here
the top ranked projects in several catagories.

<UL>
<LI><A href="toplist.php?type=downloads">Top Downloads</A>
<LI><A href="toplist.php?type=downloads_week">Top Downloads (Past 7 Days)</A>
<BR>&nbsp;
<LI><A href="toplist.php?type=pageviews_proj">Top Project Pageviews</A> -
Measured by impressions of the SourceForge 'button' logo
<BR>&nbsp;
<LI><A href="toplist.php?type=forumposts_week">Top Forum Post Counts</A>
</UL>

<?php
site_footer(array());
site_cleanup(array());
?>
