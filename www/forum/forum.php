<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: forum.php,v 1.28 2000/01/13 18:36:35 precision Exp $

require('pre.php');
require('../forum/forum_utils.php');

function show_threads($forum_id,$offset,$et=0) {
	/*
		Takes a forum_id and fetches the distinct threads in
		that forum, then invokes show_submessages to nest the threads
	*/
	global $total_rows, $sys_datefmt;

	$max_rows=25;

	$sql="SELECT user.user_name,forum.msg_id,forum.subject,forum.thread_id,forum.body,forum.date,forum.is_followup_to ".
		"FROM forum,user WHERE forum.group_forum_id='$forum_id' AND user.user_id=forum.posted_by AND forum.is_followup_to='0' ".
		"ORDER BY forum.date DESC, forum.subject ASC, forum.is_followup_to ASC LIMIT $offset,".($max_rows+1);

	//echo $sql;

	$result=db_query($sql);
	$rows=db_numrows($result);

	if ($rows > $max_rows) {
		$rows=$max_rows;
	}

	$total_rows=0;

	if (!$result || $rows < 1) {
		$ret_val .= 'No Messages in '.stripslashes(get_forum_name($forum_id));
	} else {
		//echo "\n<table cellspacing=\"0\" cellpadding=\"2\" width=\"100%\" BORDER=\"0\"><tr><td align=CENTER bgcolor=\"#666699\">".
		$ret_val .= '<TABLE WIDTH="100%" CELLPADDING="2" CELLSPACING="0" BGCOLOR="#FFFFFF" BORDER="0">
			<TR BGCOLOR="'.$GLOBALS[COLOR_MENUBARBACK].'">
			<TD WIDTH="25%"><FONT COLOR=#FFFFFF><B>Thread/Subject</TD>
			<TD><FONT COLOR=#FFFFFF><B>Author</TD>
			<TD><FONT COLOR="#FFFFFF"><B>Date/Time</TD></TR>';
		$i=0;
		while (($total_rows < $max_rows) && ($i < $rows)) {
			/*
				Colorization code
			*/
			$total_rows++;
			if ($total_rows % 2 == 0) {
				$row_color=' BGCOLOR="'.$GLOBALS[COLOR_LTBACK1].'"';
			} else {
				$row_color=' BGCOLOR="#FFFFFF"';
			}
			if ($et) {
				/*
					Build row if threads are expanded
				*/
				$ret_val .= '
					<TR'.$row_color.'><TD><A HREF="/forum/thread.php?thread_id='.
					db_result($result, $i, 'thread_id').'">'.
					'<IMG SRC="/images/ic/cfolder15.png" HEIGHT=13 WIDTH=15 BORDER=0>'.
					'</A> &nbsp;<A HREF="/forum/message.php?msg_id='.
					db_result($result, $i, 'msg_id').'">'.
					'<IMG SRC="/images/msg.gif" BORDER=0 HEIGHT=12 WIDTH=10> ';
				/*
					See if this message is new or not
				*/
				if (get_forum_saved_date($forum_id) < db_result($result,$i,'date')) { $ret_val .= '<B>'; }

				$ret_val .= stripslashes(db_result($result, $i, 'subject')).'</A></TD>'.
					'<TD>'.db_result($result, $i, 'user_name').'</TD>'.
					'<TD>'.date($sys_datefmt,db_result($result,$i,'date')).'</TD></TR>';

				/*
					Expand thread
				*/
				$ret_val .= show_submessages(db_result($result, $i, 'thread_id'),db_result($result, $i, 'msg_id'),1,0);
			} else {
				/*
					Build row for collapsed threads
				*/
				$ret_val .= '
					<TR'.$row_color.'><TD><A HREF="/forum/thread.php?thread_id='.
					db_result($result, $i, 'thread_id').'">'.
					'<IMG SRC="/images/ic/cfolder15.png" HEIGHT=13 WIDTH=15 BORDER=0> ';
				/*
					See if this message is new or not
				*/
				if (get_forum_saved_date($forum_id) < db_result($result,$i,'date')) { $ret_val .= '<B>'; }
				
				$ret_val .= db_result($result, $i, 'subject').'</A></TD>'.
					'<TD>'.db_result($result, $i, 'user_name').'</TD>'.
					'<TD>'.date($sys_datefmt,db_result($result,$i,'date')).'</TD></TR>';
			}
			$i++;
		}

		/*
			This code puts the nice next/prev.
		*/
		$ret_val .= '
				<TR BGCOLOR="#EEEEEE"><TD>';
		if ($offset != 0) {
			$ret_val .= '<FONT face="Arial, Helvetica" SIZE="3" STYLE="text-decoration: none"><B>
				<A HREF="javascript:history.back()">
				<B><IMG SRC="/images/t2.gif" HEIGHT=15 WIDTH=15 BORDER=0 ALIGN=MIDDLE> Previous Messages</A></B></FONT>';
		} else {
			$ret_val .= '&nbsp;';
		}

		$ret_val .= '</TD><TD>&nbsp;</TD><TD>';
		if (db_numrows($result) > $i) {
			$ret_val .= '<FONT face="Arial, Helvetica" SIZE=3 STYLE="text-decoration: none"><B>
				<A HREF="/forum/forum.php?et='.$et.'&offset='.($offset+$i).'&forum_id='.$forum_id.'">
				<B>Next Messages <IMG SRC="/images/t.gif" HEIGHT=15 WIDTH=15 BORDER=0 ALIGN=MIDDLE></A>';
		} else {
			$ret_val .= '&nbsp;';
		}

		$ret_val .= '</TABLE>';//</TD></TR></TABLE>";
	}

	return $ret_val;

}


if ($forum_id) {
	/*
		if necessary, insert the new message
	*/
	if ($post_message == "y") {
		post_message($thread_id, $is_followup_to, $subject, $body, $forum_id);
	}

	if ((!$offset) || ($offset < 0)) {
		$offset=0;
	} 

	/*
		Set up navigation vars
	*/
	$result=db_query("SELECT group_id,forum_name,is_public FROM forum_group_list WHERE group_forum_id='$forum_id'");

	$group_id=db_result($result,0,'group_id');
	$forum_name=db_result($result,0,'forum_name');

	forum_header(array('title'=>$forum_name));

	if (!user_isloggedin() || !user_ismember($group_id)) {
		/*
			If this is a private forum, kick 'em out
		*/
		if (db_result($result,0,'is_public') == '0') {
			echo "<h1>Forum is restricted</H1>";
			forum_footer(array());
			exit;
		}
	}

	echo show_threads($forum_id, $offset, $et);

	echo "<P>&nbsp;<P>";

	echo "<CENTER><h3>Start a New Thread:</H3></CENTER>";
	show_post_form($forum_id);

	forum_footer(array());

} else {

	forum_header(array("title"=>"Error"));
	echo "<H1>Error - choose a forum first</H1>";
	forum_footer(array());

}

?>
