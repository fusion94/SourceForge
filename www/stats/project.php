<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: project.php,v 1.5 2000/01/30 09:55:04 precision Exp $ 

require('pre.php');
require('cache.php');

site_header(array('title'=>'Site Statistics'));

echo '<P><B><A HREF="/stats/browser.php">Browser Stats</A> | <A HREF="/stats/project.php">Project Stats</A> | <A HREF="/stats/">SourceForge Stats</A> </B>';
echo '<P>';

echo cache_display("stats_project_stats","stats_project_stats()",1800);

site_footer(array());
?>
