<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: html.php,v 1.243 2000/01/13 18:36:35 precision Exp $


// ********************* backgrounds

/*
$COLOR_LTBACK1  = '#eeebee';
$COLOR_MENUBACK = '#eeebee';
$COLOR_MENUBARBACK = '#7a6672';
*/

$COLOR_LTBACK1  = '#eeeef8';
$COLOR_MENUBACK = $COLOR_LTBACK1;
$COLOR_MENUBARBACK = '737b9c';

$BARBACK = ' bgcolor="'.$COLOR_BARBACK.'" ';


function html_feedback_top($feedback) {
	if (!$feedback) return 0;

	print '
		<TABLE width="100%" cellspacing=0 cellpadding=1 border=0 bgcolor="#FFFFFF">
		<TR><TD align=center><FONT color="#FF0000"><H3>'.$feedback.'</H3></FONT></TD></TR>
		<TR><TD bgcolor="#000000">';
	html_blankimage(2,1);
	print '</TD></TR></TABLE>';
}

function html_feedback_bottom($feedback) {
	if (!$feedback) return 0;

	print '
		<TABLE width="100%" cellspacing=0 cellpadding=1 border=0 bgcolor="#FFFFFF">
		<TR><TD bgcolor="#000000">';
	html_blankimage(2,1);
	print '</TD></TR><TR><TD align=center><FONT color="#FF0000"><H3>'.$feedback.'</H3></FONT></TD></TR>
		</TABLE>';
}

// ******************************* those cool purple headers

function html_box1_top($title,$echoout=1) {
	$return = '<TABLE cellspacing="0" cellpadding="1" width="100%" border="0" bgcolor="'
		.$GLOBALS[COLOR_MENUBARBACK].'"><TR><TD>';

	$return .= '<TABLE cellspacing="1" cellpadding="2" width="100%" border="0" bgcolor="#FFFFFF">'
		.'<TR BGCOLOR="'.$GLOBALS[COLOR_MENUBARBACK].'" align="center">'
		.'<TD colspan=2><SPAN class=titlebar>'.$title.'</SPAN></TD></TR>'
		.'<TR align=left><TD colspan=2>';
	if ($echoout) {
		print $return;
	} else {
		return $return;
	}
}

function html_box1_bottom($echoout=1) {
	$return = '
		</TD></TR></TABLE>
		</TD></TR></TABLE><P>';
	if ($echoout) {
		print $return;
	} else {
		return $return;
	}
}

// ########################## for environment boxes

function html_displayenvironments($group_id) {
	global $ENVFILE,$SOFTENV;
	$return = '';
	$res_grp = db_query('SELECT * FROM group_env WHERE group_id='.$group_id.' ORDER BY env_id');
	if (db_numrows($res_grp) < 1) return html_image('ic/env-oth.png',array(),0);

	while ($row_grp = db_fetch_array($res_grp)) {
		$return .= html_image('ic/'.$ENVFILE["$row_grp[env_id]"],array(alt=>$SOFTENV["$row_grp[env_id]"]),0);
	}

	return $return;
}

// ########################## for languages 

function html_displaylanguages($group_id) {
	global $SOFTLANG;
	$return = '';
	$res_grp = db_query('SELECT * FROM group_language WHERE group_id='.$group_id.' ORDER BY language_id');
	if (db_numrows($res_grp) < 1) return '';

	while ($row_grp = db_fetch_array($res_grp)) {
		if ($return) $return .= ', ';
		$return .= $SOFTLANG["$row_grp[language_id]"];
	}

	return "($return)";
}

// **************************** functions for users/groups/cats

function html_a_developer($user) {
	print '<A href="/developer/?form_dev='.$user.'">'.user_getname($user).'</A>';
}


function html_a_group($grp) {
	print '<A href="/project/?group_id='.$grp.'">' . group_getname($grp) . '</A>';
}

function html_a_category($cat) {
	print '<A href="/softwaremap/?form_cat='.$cat.'">' . category_getname($cat) . '</A>';
}

// *************************** just need this one

function html_blankimage($height,$width) {
	html_image('blank.gif',array('height'=>$height,'width'=>$width));
}

// ************************* for alternating colored rows

function html_colored_tr($args = '') {
	if ($GLOBALS[html_colored_tr] == 0) {
		print '<TR bgcolor="'.$GLOBALS[COLOR_LTBACK1].'" '.$args.'>';
		$GLOBALS[html_colored_tr] = 1;
	} else {
		print '<TR bgcolor="#FFFFFF" '.$args.'>';	
		$GLOBALS[html_colored_tr] = 0;
	}
}

function html_colored_tr_toggle() {
	if ($GLOBALS[html_colored_tr] == 0) {
		$GLOBALS[html_colored_tr] = 1;
	} else {
		$GLOBALS[html_colored_tr] = 0;
	}
}

// ################################ font stuff

function html_font($type = 'body') {
	switch ($type) {
		case 'body': print '<font color="#333333" face="arial,helvetica,sanserif">';
			break;
		default: print '<font color="#333333" face="arial,helvetica,sanserif">';
	}
}

// ################################## function browser_png()
// ## returns true if browser is png capable

function browser_png() {
	$accept = getenv('HTTP_ACCEPT');
	if (ereg('image/png',$accept,$ereg_match)) {
		return 1;
	} else {
		return 0;
	}
}

// ################################## function image_pngit
// ## returns either the gif or png, depending on browser

function image_pngit($src) {
	if (browser_png()) {
		$image = explode('.',$src);
		return ($image[0] . '.png');
	} else {
		return ($src);
	}
}

// ################################# function html_image
// ## for images

function html_image($src,$args,$display=1)
{
	$return = '';

	// cant use image server for secure
	if (session_issecure()) {
		$return .= ('<IMG src="/images/'.$src.'"');
	} else {
		$return .= ('<IMG src="http://images.sourceforge.net/'.$src.'"');
	}
	reset($args);
	while(list($k,$v) = each($args)) {
		$return .= ' '.$k.'="'.$v.'"';
	}
	
	// ## insert a border tag if there isn't one
	if (!$args[border]) $return .= (" border=0");
	
	// ## if no height AND no width tag, insert em both
	if (!$args[height] && !$args[width]) {
		$size = getimagesize($GLOBALS[sys_urlroot].'images/'.$src);
		$return .= ' ' . $size[3];
	}
	
	$return .= ('>');
	if ($display) {
		print $return;
	} else {
		return $return;
	}
}

// ################################### HTML tabs

function html_tabs($toptab,$group) {
	// get group info
	$res_grp = db_query('SELECT group_name,status,homepage,option_bugs FROM groups WHERE group_id='.$group);

	if (db_numrows($res_grp) < 1) {
		return;
	}

	$row_grp = db_fetch_array($res_grp);

	// software map trail
	// print group_fullname($group)."\n<BR>";

	// common html table code
	print '
		<TABLE width="100%" border="0" cellspacing="0" cellpadding="0"><TR>';

	// header text
	print '<TD align="left"><FONT size="+2"><B>'.$row_grp[group_name].' - ';
	// specific to where we're at
	switch ($toptab) {
		case 'home': print 'Summary'; break;
		case 'forums': print 'Message Forums'; break;
		case 'bugs': print 'Bug Tracking'; break;
		case 'mail': print 'Mailing Lists'; break;
		case 'pm': print 'Task Manager'; break;
		case 'surveys': print 'Surveys'; break;
		case 'cvs': print 'CVS'; break;
		case 'downloads': print 'Downloads'; break;
		case 'news': print 'News'; break;
		default: print 'Summary'; break;
	}
	print '</B></FONT></TD><TD align="right">';

	// Summary
	print ' 
		<A ';
	if ($toptab == 'home') 
		print 'class=tabs ';
	print 'href="/project/?group_id='.$group.'">';
	html_image('ic/anvil24.png',array(alt=>'Summary',border=>(($toptab=='home')?'1':'0')));
	//print "<B>Summary</B>";
	print '</A>';

	//print " | ";
	// Homepage	
	print ' 
		<A ';
	print 'href="http://'.$row_grp[homepage].'">';
	html_image('ic/home.png',array(alt=>'Homepage',border=>0));
	//print "<B>Forums</B>";
	print '</A>';


	// Forums	
	print ' 
		<A ';
	if ($toptab == 'forums') 
		print 'class=tabs ';
	print 'href="/forum/?group_id='.$group.'">';
	html_image('ic/notes.png',array(alt=>'Message Forums',border=>(($toptab=='forums')?'1':'0')));
	//print "<B>Forums</B>";
	print '</A>';

	//print " | ";

	// Bug Tracking
	if (($row_grp[status] == 'A') && ($row_grp[option_bugs])) {
		print ' 
			<A ';	
		if ($toptab == 'bugs') 
			print 'class=tabs ';
		print 'href="/bugs/?group_id='.$group.'">';
		html_image('ic/bug.png',array(alt=>'Bug Tracking','border'=>(($toptab=='bugs')?'1':'0')));
		//print "<B>Bug Tracking</B>";
		print '</A>';

		//print " | ";
	}

	// Mailing Lists
	if ($row_grp[status] == 'A') {
		print ' 
			<A ';	
		if ($toptab == 'mail') print 'class=tabs ';
		print 'href="/mail/?group_id='.$group.'">';
		html_image('ic/mail.png',array(alt=>'Mailing Lists','border'=>(($toptab=='mail')?'1':'0')));
		//print "<B>Mail Lists</B>";
		print '</A>';

		//print " | ";
	}

	// Project Manager
	if ($row_grp[status] == 'A') {
		print ' 
			<A ';	
		if ($toptab == 'pm') 
			print 'class=tabs ';
		print 'href="/pm/?group_id='.$group.'">';
		html_image('ic/index.png',array(alt=>'Task Manager','border'=>(($toptab=='pm')?'1':'0')));
		//print "<B>Task Manager</B>";
		print "</A>";

		//print " | ";
	}

	// Surveys
	if ($row_grp[status] == 'A') {
		print ' 
			<A ';
		if ($toptab == 'surveys') 
			print 'class=tabs ';
		print 'href="/survey/?group_id='.$group.'">';
		html_image('ic/survey.png',array(alt=>'Surveys','border'=>(($toptab=='surveys')?'1':'0')));
		//print "<B>Surveys</B>";
		print '</A>';

		//print " | ";
	}

	//newsbytes
	if ($row_grp[status] == 'A') {
		print '
			<A ';
		if ($toptab == 'news')
			print 'class=tabs ';
		print 'href="/news/?group_id='.$group.'">';
		html_image('ic/news.png',array(alt=>'News','border'=>(($toptab=='news')?'1':'0')));
		//print "<B>Surveys</B>";
		print '</A>';

		//print " | ";
	}

	// CVS
	if ($row_grp[status] == 'A') {
		print ' 
			<A ';
		if ($toptab == 'cvs') 
			print 'class=tabs ';
		print 'href="/cvs/?group_id='.$group.'">';
		html_image('ic/convert.png',array(alt=>'CVS Code Repository','border'=>(($toptab=='cvs')?'1':'0')));
		//print "<B>Surveys</B>";
		print '</A>';

		//print " | ";
	}

	// Downloads
	print ' 
		<A ';
	if ($toptab == 'downloads') 
		print 'class=tabs ';
	print 'href="/project/filelist.php?group_id='.$group.'">';
	html_image('ic/save.png',array(alt=>'Downloads','border'=>(($toptab=='downloads')?'1':'0')));
	//print "<B>Downloads</B>";
	print '</A>';

	// common table code
	print '</TD></TR>';
	// bottom rule
	print '
		<TR><TD colspan="2">';
	html_blankimage(4,1);
	print '</TD></TR>';
	print '
		<TR bgcolor="#000000"><TD colspan="2">';
	html_blankimage(2,1);
	print '</TD></TR>';
	print '
		</TABLE>
		<BR>';
}

?>
