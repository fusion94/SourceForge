<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: index.php,v 1.22 2000/12/13 22:33:25 dbrogdon Exp $

require('pre.php');
require('../mail/mail_utils.php');

if ($group_id) {

	mail_header(array('title'=>'Mailing Lists for '.group_getname($group_id)));
	
	if (user_isloggedin() && user_ismember($group_id)) {
		$public_flag='0,1';
	} else {
		$public_flag='1';
	}

	$sql="SELECT * FROM mail_group_list WHERE group_id='$group_id' AND is_public IN ($public_flag)";

	$result = db_query ($sql);

	$rows = db_numrows($result); 

	if (!$result || $rows < 1) {
		echo '
			<H1>No Lists found for '.group_getname($group_id).'</H1>';
		echo '
			<P>Project administrators use the admin link to request mailing lists.';
		$HTML->footer(array());
		exit;
	}

	echo "<P>Mailing lists provided via a SourceForge version of "
		. "<A href=\"http://www.list.org\">GNU Mailman</A>. "
		. "Thanks to the Mailman and <A href=\"http://www.python.org\">Python</A> "
		. "crews for excellent software.";
	echo "<P>Choose a list to browse, search, and post messages.<P>\n";

	/*
		Put the result set (list of mailing lists for this group) into a column with folders
	*/

	echo "<table WIDTH=\"100%\" border=0>\n".
		"<TR><TD VALIGN=\"TOP\">\n"; 

	for ($j = 0; $j < $rows; $j++) {
		echo '<A HREF="http://www.geocrawler.com/redir-sf.php3?list='.
			db_result($result, $j, 'list_name').'">' . 
			html_image("images/ic/cfolder15.png","15","13",array("BORDER"=>"0")) . ' &nbsp; '.
			db_result($result, $j, 'list_name').' Archives</A>'; 
		echo ' (go to <A HREF="http://'.$GLOBALS['sys_lists_host'].'/mailman/listinfo/'.
			db_result($result, $j, 'list_name').'">Subscribe/Unsubscribe/Preferences</A>)<BR>';
		echo '&nbsp;'.  db_result($result, $j, 'description') .'<P>';
	}
	echo '</TD></TR></TABLE>';

} else {
	mail_header(array('title'=>'Choose a Group First'));
	require('../mail/mail_nav.php');
	echo '
		<H1>Error - choose a group first</H1>';
}
mail_footer(array()); 

?>
