<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: index.php,v 1.15 2000/03/08 19:10:04 tperdue Exp $ 

require('pre.php');
require('cache.php');

site_header(array('title'=>'Site Statistics'));

echo '<P><B> <A HREF="/stats/project.php">Project Stats</A> | <A HREF="/stats/">SourceForge Stats</A> </B>';
echo '<P>';

echo cache_display("stats_sf_stats",'1',1800);

site_footer(array());
?>
