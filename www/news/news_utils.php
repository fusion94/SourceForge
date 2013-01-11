<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: news_utils.php,v 1.23 2000/01/13 18:36:35 precision Exp $

function news_header($params) {
	global $DOCUMENT_ROOT,$group_id,$news_name,$news_id;
	$params['group']=$group_id;
	site_header($params);

	/*
		Show horizontal links
	*/
	if ($group_id && ($group_id != 714)) {
		html_tabs('news',$group_id);
	} else {
		echo '
			<H2>SourceForge <A HREF="/news/">News</A></H2>';
	}
	echo '<P><B>';
	echo '<A HREF="/news/submit.php?group_id='.$group_id.'">Submit</A> | <A HREF="/news/admin/?group_id='.$group_id.'">Admin</A></B>';
	echo '<P>';
}

function news_footer($params) {
	global $feedback;
	html_feedback_bottom($feedback);
	site_footer($params);
}

function news_show_latest($group_id=714) {
	global $sys_datefmt;
	/*
		Show a simple list of the latest news items with a link to the forum
	*/

	if ($group_id != 714) {
		$wclause="news_bytes.group_id='$group_id'";
	} else {
		$wclause='news_bytes.is_approved=1';
	}

	$sql="SELECT user.user_name,news_bytes.forum_id,news_bytes.summary,news_bytes.date ".
		"FROM user,news_bytes ".
		"WHERE $wclause AND user.user_id=news_bytes.submitted_by ".
		"ORDER BY date DESC LIMIT 10";

	$result=db_query($sql);
	$rows=db_numrows($result);

	$return .= html_box1_top('Latest News',0);

	if (!$result || $rows < 1) {
		$return .= '<H3>No News Items Found</H3>';
		$return .= db_error();
	} else {
		echo '
			<UL>';
		for ($i=0; $i<$rows; $i++) {
			$return .= '
				<LI><A HREF="/forum/forum.php?forum_id='.db_result($result,$i,'forum_id').'"><B>'.stripslashes(db_result($result,$i,'summary')).'</B></A>
				<BR>&nbsp; &nbsp; &nbsp;<FONT SIZE="-1">'.db_result($result,$i,'user_name').' - '.
					date($sys_datefmt,db_result($result,$i,'date')).'</FONT></LI>';
		}
		echo '
			</UL>';
	}
	$return .= '<P><A HREF="/news/submit.php?group_id='.$group_id.'"><B>Submit News</B></A><BR>&nbsp;';
	$return .= html_box1_bottom(0);
	return $return;
}

function get_news_name($id) {
	/*
		Takes an ID and returns the corresponding forum name
	*/
	$sql="SELECT summary FROM news_bytes WHERE id='$id'";
	$result=db_query($sql);
	if (!$result || db_numrows($result) < 1) {
		return "Not Found";
	} else {
		return db_result($result, 0, 'summary');
	}
}

?>
