<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: html.php,v 1.291 2000/07/12 21:01:40 tperdue Exp $

$COLOR_LTBACK1  = '#EEEEF8';
$COLOR_LTBACK2  = '#F6F6F6';

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

function html_box1_top($title,$echoout=1,$bgcolor='#FFFFFF') {
	$return = '
		<TABLE cellspacing="0" cellpadding="1" width="100%" border="0" bgcolor="'
		.$GLOBALS['COLOR_MENUBARBACK'].'"><TR><TD>';

	$return .= '<TABLE cellspacing="1" cellpadding="2" width="100%" border="0" bgcolor="'.$bgcolor.'">'.
			'<TR BGCOLOR="'.$GLOBALS['COLOR_MENUBARBACK'].'" align="center">'.
			'<TD colspan=2><SPAN class=titlebar>'.$title.'</SPAN></TD></TR>'.
			'<TR align=left>
				<TD colspan=2>';
	if ($echoout) {
		print $return;
	} else {
		return $return;
	}
}

function html_box1_middle($title,$bgcolor='#FFFFFF') {
	return '
				</TD>
			</TR>
			<TR BGCOLOR="'.$GLOBALS['COLOR_MENUBARBACK'].'" align="center">
				<TD colspan=2><SPAN class=titlebar>'.$title.'</SPAN></TD>
			</TR>
			<TR align=left bgcolor="'.$bgcolor.'">
				<TD colspan=2>';
}

function html_box1_bottom($echoout=1) {
	$return = '
				</TD>
			</TR>
		</TABLE></TD></TR></TABLE><P>';
	if ($echoout) {
		print $return;
	} else {
		return $return;
	}
}

function html_a_group($grp) {
	print '<A /project/?group_id='.$grp.'">' . group_getname($grp) . '</A>';
}

function html_blankimage($height,$width) {
	return html_image('blank.gif',array('height'=>$height,'width'=>$width));
}

function html_image($src,$args,$display=1) {
	$return = ('<IMG src="/images/'.$src.'"');
	reset($args);
	while(list($k,$v) = each($args)) {
		$return .= ' '.$k.'="'.$v.'"';
	}

	// ## insert a border tag if there isn't one
	if (!$args['border']) $return .= (" border=0");

	// ## if no height AND no width tag, insert em both
	if (!$args['height'] && !$args['width']) {
		$size = getimagesize($GLOBALS['sys_urlroot'].'images/'.$src);
		$return .= ' ' . $size[3];
	}

	// ## insert alt tag if there isn't one
	if (!$args['alt']) $return .= " alt=\"$src\"";

	$return .= ('>');
	if ($display) {
		print $return;
	} else {
		return $return;
	}
}

function html_tabs($toptab,$group_id) {

	/*
		See if this is a portal or a project
	*/
	if (group_get_type_id ($group_id) == 1) {
		//this is a project
		echo html_project_tabs($toptab,$group_id);
	} else {
		echo html_portal_tabs($toptab,$group_id);
	}
}

function html_portal_tabs($toptab,$group) {

	// get group info using the common result set
	$result = group_get_result($group);

	if (db_numrows($result) < 1) {
		return;
	}

	// common html table code
	print '
		<TABLE width="100%" border="0" cellspacing="0" cellpadding="0"><TR>';

	// header text
	print '<TD align="left"><FONT size="+2"><B>'.db_result($result,0,'group_name').' - ';
	// specific to where we're at
	switch ($toptab) {
		case 'home': print 'Summary'; break;
		case 'forums': print 'Message Forums'; break;
		case 'surveys': print 'Surveys'; break;
		case 'news': print 'News'; break;
		default: print 'Summary'; break;
	}
	print '</B></FONT></TD><TD align="right">';

	// Summary
	print '
		<A ';
	if ($toptab == 'home')
		print 'class=tabs ';
	print 'href="/portals/'. strtolower(db_result($result,0,'unix_group_name')) .'/">';
	html_image('ic/anvil24.png',array('alt'=>'Summary','border'=>(($toptab=='home')?'1':'0')));
	print '</A>';


	print '
		<A ';
	if ($toptab == 'forums')
		print 'class=tabs ';
	print 'href="/forum/?group_id='.$group.'">';
	html_image('ic/notes.png',array('alt'=>'Message Forums','border'=>(($toptab=='forums')?'1':'0')));
	print '</A>';


	print '
		<A ';
	if ($toptab == 'surveys')
		print 'class=tabs ';
	print 'href="/survey/?group_id='.$group.'">';
	html_image('ic/survey.png',array('alt'=>'Surveys','border'=>(($toptab=='surveys')?'1':'0')));
	print '</A>';


	print '
		<A ';
	if ($toptab == 'news')
		print 'class=tabs ';
	print 'href="/news/?group_id='.$group.'">';
	html_image('ic/news.png',array('alt'=>'News','border'=>(($toptab=='news')?'1':'0')));
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

function html_project_tabs($toptab,$group) {

	// get group info using the common result set
	$result = group_get_result($group);

	if (db_numrows($result) < 1) {
		return;
	}

	// common html table code
	print '
		<TABLE width="100%" border="0" cellspacing="0" cellpadding="0"><TR>';

	// header text
	print '<TD align="left"><FONT size="+2"><B>'.db_result($result,0,'group_name').' - ';
	// specific to where we're at
	switch ($toptab) {
		case 'home': print 'Summary'; break;
		case 'forums': print 'Message Forums'; break;
		case 'bugs': print 'Bug Tracking'; break;
		case 'support': print 'Tech Support Manager'; break;
		case 'mail': print 'Mailing Lists'; break;
		case 'pm': print 'Task Manager'; break;
		case 'docman': print 'Doc Manager'; break;
		case 'surveys': print 'Surveys'; break;
		case 'cvs': print 'CVS'; break;
		case 'downloads': print 'Downloads'; break;
		case 'news': print 'News'; break;
		case 'patch': print 'Patch Manager'; break;
		default: print 'Summary'; break;
	}
	print '</B></FONT></TD><TD align="right">';

	// Summary
	print '
		<A ';
	if ($toptab == 'home')
		print 'class=tabs ';
	print 'href="/projects/'. strtolower(db_result($result,0,'unix_group_name')) .'/">';
	html_image('ic/anvil24.png',array('alt'=>'Summary','border'=>(($toptab=='home')?'1':'0')));
	print '</A>';

	print '
		<A ';
	print 'href="http://'.db_result($result,0,'homepage').'">';
	html_image('ic/home.png',array('alt'=>'Homepage','border'=>'0'));
	print '</A>';

	// Forums
	if ((db_result($result,0,'status') == 'A') && (db_result($result,0,'use_forum'))) {
		print '
			<A ';
		if ($toptab == 'forums')
			print 'class=tabs ';
		print 'href="/forum/?group_id='.$group.'">';
		html_image('ic/notes.png',array('alt'=>'Message Forums','border'=>(($toptab=='forums')?'1':'0')));
		print '</A>';
	}

	// Bug Tracking
	if ((db_result($result,0,'status') == 'A') && (db_result($result,0,'use_bugs'))) {
		print '
			<A ';
		if ($toptab == 'bugs')
			print 'class=tabs ';
		print 'href="/bugs/?group_id='.$group.'">';
		html_image('ic/bug.png',array('alt'=>'Bug Tracking','border'=>(($toptab=='bugs')?'1':'0')));
		print '</A>';
	}

	// Support Tracking
	if ((db_result($result,0,'status') == 'A') && (db_result($result,0,'use_support'))) {
		print '
			<A ';
		if ($toptab == 'support')
			print 'class=tabs ';
		print 'href="/support/?group_id='.$group.'">';
		html_image('ic/support.png',array('alt'=>'Tech Support Manager','border'=>(($toptab=='support')?'1':'0')));
		print '</A>';
	}

	// Patch Manager
	if ((db_result($result,0,'status') == 'A') && (db_result($result,0,'use_patch'))) {
		print '
			<A ';
		if ($toptab == 'patch')
			print 'class=tabs ';
		print 'href="/patch/?group_id='.$group.'">';
		html_image('ic/patch.png',array('alt'=>'Patch Manager','border'=>(($toptab=='patch')?'1':'0')));
		print '</A>';
	}

	// Mailing Lists
	if ((db_result($result,0,'status') == 'A') && (db_result($result,0,'use_mail'))) {
		print '
			<A ';
		if ($toptab == 'mail') print 'class=tabs ';
		print 'href="/mail/?group_id='.$group.'">';
		html_image('ic/mail.png',array('alt'=>'Mailing Lists','border'=>(($toptab=='mail')?'1':'0')));
		print '</A>';
	}

	// Project Manager
	if ((db_result($result,0,'status') == 'A') && (db_result($result,0,'use_pm'))) {
		print '
			<A ';
		if ($toptab == 'pm')
			print 'class=tabs ';
		print 'href="/pm/?group_id='.$group.'">';
		html_image('ic/index.png',array('alt'=>'Task Manager','border'=>(($toptab=='pm')?'1':'0')));
		print "</A>";
	}

	// Doc Manager
	if ((db_result($result,0,'status') == 'A') && (db_result($result,0,'use_docman'))) {
		print '
			<A ';
		if ($toptab == 'docman')
			print 'class=tabs ';
		print 'href="/docman/?group_id='.$group.'">';
		html_image('ic/docman.png',array('alt'=>'Doc Manager','border'=>(($toptab=='docman')?'1':'0')));
		print "</A>";
	}

	// Surveys
	if ((db_result($result,0,'status') == 'A') && (db_result($result,0,'use_survey'))) {
		print '
			<A ';
		if ($toptab == 'surveys')
			print 'class=tabs ';
		print 'href="/survey/?group_id='.$group.'">';
		html_image('ic/survey.png',array('alt'=>'Surveys','border'=>(($toptab=='surveys')?'1':'0')));
		print '</A>';
	}

	//newsbytes
	if ((db_result($result,0,'status') == 'A') && (db_result($result,0,'use_news'))) {
		print '
			<A ';
		if ($toptab == 'news')
			print 'class=tabs ';
		print 'href="/news/?group_id='.$group.'">';
		html_image('ic/news.png',array('alt'=>'News','border'=>(($toptab=='news')?'1':'0')));
		print '</A>';
	}

	// CVS
	if ((db_result($result,0,'status') == 'A') && (db_result($result,0,'use_cvs'))) {
		print '
			<A ';
		if ($toptab == 'cvs')
			print 'class=tabs ';
		print 'href="/cvs/?group_id='.$group.'">';
		html_image('ic/convert.png',array('alt'=>'CVS Code Repository','border'=>(($toptab=='cvs')?'1':'0')));
		print '</A>';
	}

	// Downloads
	print '
		<A ';
	if ($toptab == 'downloads') {
		print 'class=tabs ';
	}
	print 'href="/project/filelist.php?group_id='.$group.'">';
	html_image('ic/save.png',array('alt'=>'Downloads','border'=>(($toptab=='downloads')?'1':'0')));
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
