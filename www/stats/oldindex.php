<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: oldindex.php,v 1.1 2000/08/31 22:40:16 msnelham Exp $ 

require('pre.php');
require('cache.php');

session_require(array('group'=>'1','admin_flags'=>'A'));

$HTML->header(array('title'=>'Site Statistics'));

echo '<P><B> <A HREF="/stats/project.php">Project Stats</A> | <A HREF="/stats/">SourceForge Stats</A> </B>';
echo '<P>';

echo cache_display("stats_sf_stats",'1',1800);

$HTML->footer(array());
?>
