<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: index.php,v 1.10 2000/06/05 12:20:31 tperdue Exp $

require('pre.php');
require('../people/people_utils.php');

people_header(array('title'=>'Help Wanted System'));

if ($group_id) {

	//html_tabs('home',$group_id);

	echo '<H3>Project Help Wanted for '. group_getname($group_id) .'</H3>
	<P>
	Here is a list of positions available for this project.
	<P>';

	echo people_show_project_jobs($group_id);
	
} else if ($category_id) {

	echo '<H3>Projects looking for '. people_get_category_name($category_id) .'</H3>
		<P>
		Click job titles for more detailed descriptions.
		<P>';
	echo people_show_category_jobs($category_id);

} else {

	echo '
	<H3>Projects Needing Help</H3>
	<P>
	Browse through the category menu to find projects looking for your help.
	<P>
	If you\'re a project admin, log in and submit help wanted requests through
	your project page.
	<P>
	To suggest new job categories, visit the support manager
		<P>';
	echo people_show_category_table();

}

people_footer(array());

?>
