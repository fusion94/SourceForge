<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: index.php,v 1.9 2000/11/12 21:51:13 tperdue Exp $

require ('vars.php');
require ('pre.php');
require ('../snippet/snippet_utils.php');
require ('cache.php');
require ('snippet_caching.php');

snippet_header(array('title'=>'Snippet Library', 'header'=>'Snippet Library'));

echo cache_display('snippet_mainpage','snippet_mainpage()',7200);

snippet_footer(array());

?>
