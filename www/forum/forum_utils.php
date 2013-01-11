<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: forum_utils.php,v 1.142 2000/04/20 17:25:40 tperdue Exp $

/*
	Message Forums
	By Tim Perdue, Sourceforge, 11/99
*/

require($DOCUMENT_ROOT.'/news/news_utils.php');

function forum_header($params) {
	global $DOCUMENT_ROOT,$group_id,$forum_name,$thread_id,$msg_id,$forum_id,$REQUEST_URI,$sys_datefmt,$et,$et_cookie;
	$params['group']=$group_id;
	site_header($params);

	/*
		Show icon bar unless it's a news forum
	*/
	if ($group_id == 714) {
		if ($forum_id) {
			/*
				Show this news item at the top of the page
			*/
			$sql="SELECT * FROM news_bytes WHERE forum_id='$forum_id'";
			$result=db_query($sql);

	       		if (db_result($result,0,'group_id') != 714) {
				html_tabs('news',db_result($result,0,'group_id'));
			} else {
				echo '
					<H2>SourceForge <A HREF="/news/">News</A></H2><P>';
			}

			echo '<TABLE><TR><TD VALIGN="TOP">';
			if (!$result || db_numrows($result) < 1) {
				echo '
					<h3>Error - this news item was not found</h3>';
			} else {
				echo '
				<B>Posted By:</B> '.user_getname( db_result($result,0,'submitted_by')).'<BR>
				<B>Date:</B> '. date($sys_datefmt,db_result($result,0,'date')).'<BR>
				<B>Summary:</B><A HREF="/forum/forum.php?forum_id='.db_result($result,0,'forum_id').'">'. db_result($result,0,'summary').'</A>
				<P>
				'. util_make_links( nl2br( db_result($result,0,'details')));

				echo '<P>';
			}
			echo '</TD><TD VALIGN="TOP" WIDTH="35%">';
			echo news_show_latest();
			echo '</TD></TR></TABLE>';
		}
	} else {
		html_tabs('forums',$group_id);
	}

	/*
		Show horizontal links
	*/
	if ($forum_id && $forum_name) {
		echo '<P><H3>Discussion Forums: <A HREF="/forum/forum.php?forum_id='.$forum_id.'">'.$forum_name.'</A></H3>';
	}
	echo '<P><B>';

	if ((user_get_preference('forum_expand')  == 1) && $forum_id) {
		echo '<A HREF="/forum/expand.php?et=0&forum_id='.$forum_id.'">Collapsed View</A>';
		$et=1;
	} else if($forum_id) {
		echo '<A HREF="/forum/expand.php?et=1&forum_id='.$forum_id.'">Expanded View</A>';
		$et=0;
	}

	if ($forum_id) {
		echo ' | <A HREF="/forum/monitor.php?forum_id='.$forum_id.'">';
		echo html_image("ic/check.png",array()).' Monitor Forum</A>
			 | <A HREF="/forum/save.php?forum_id='.$forum_id.'">Save Place</A>';
	}

	if ($forum_id || $msg_id || $thread_id) {
		echo ' | <A HREF="#_post">Post</A> | ';
	}

	echo '  <A HREF="/forum/admin/?group_id='.$group_id.'">Admin</A></B>';
	echo '<P>';
}

function forum_footer($params) {
	global $feedback;
	html_feedback_bottom($feedback);
	site_footer($params);
}

function forum_create_forum($group_id,$forum_name,$is_public=1,$create_default_message=1) {
	global $feedback;
	/*
		Adding forums to this group
	*/
	$sql="INSERT INTO forum_group_list VALUES ('','$group_id','$forum_name','$is_public')";

	$result=db_query($sql);
	if (!$result) {
		$feedback .= " Error Adding Forum ";
	} else {
		$feedback .= " Forum Added ";
	}
	$forum_id=db_insertid($result);

	if ($create_default_message) {
		//set up a cheap default message
		$result2=db_query("INSERT INTO forum ".
			"(group_forum_id,posted_by,subject,body,date,is_followup_to,thread_id) ".
			"VALUES ('$forum_id','100','Welcome to $forum_name',".
			"'Welcome to $forum_name','".time()."','0','".get_next_thread_id()."')");
	}
	return $forum_id;
}

function make_links ($data="") {
	//moved make links to /include/utils.php
	util_make_links($data);
}

function get_forum_name($id) {
	/*
		Takes an ID and returns the corresponding forum name
	*/
	$sql="SELECT forum_name FROM forum_group_list WHERE group_forum_id='$id'";
	$result=db_query($sql);
	if (!$result || db_numrows($result) < 1) {
		return "Not Found";
	} else {
		return db_result($result, 0, "forum_name");
	}

}

function show_thread($thread_id,$et=0) {
	/*
		Takes a thread_id and fetches it, then invokes show_submessages to nest the threads

		$et is whether or not the forum is "expanded" or in flat mode
	*/
	global $total_rows,$sys_datefmt,$is_followup_to,$subject,$forum_id;

	$sql="SELECT user.user_name,forum.msg_id,forum.subject,forum.thread_id,forum.body,forum.date,forum.is_followup_to ".
		"FROM forum,user WHERE forum.thread_id='$thread_id' AND user.user_id=forum.posted_by AND forum.is_followup_to='0' ".
		"ORDER BY forum.date DESC, forum.subject ASC, forum.is_followup_to ASC;";

	$result=db_query($sql);

	$total_rows=0;

	if (!$result || db_numrows($result) < 1) {
		return 'Broken Thread';
	} else {
		$ret_val .= '
			<TABLE WIDTH="100%" CELLPADDING="2" CELLSPACING="0" BGCOLOR="#FFFFFF" BORDER="0">
			<TR BGCOLOR="'. $GLOBALS['COLOR_MENUBARBACK'] .'"><TD WIDTH="25%"><FONT COLOR="#FFFFFF"><B>Thread/Subject</TD>
			<TD><FONT COLOR="#FFFFFF"><B>Author</TD>
			<TD><FONT COLOR="#FFFFFF"><B>Date/Time</TD></TR>';
		$rows=db_numrows($result);
		$is_followup_to=db_result($result, ($rows-1), 'msg_id');
		$subject=db_result($result, ($rows-1), 'subject');
/*
	Short - term compatibility fix. Leaving the iteration in for now -
	will remove in the future. If we remove now, some messages will become hidden

	No longer iterating here. There should only be one root message per thread now.
	Messages posted at the thread level are shown as followups to the first message
*/
		for ($i=0; $i<$rows; $i++) {
			$total_rows++;
			$ret_val .= '<TR BGCOLOR="'. util_get_alt_row_color($total_rows) .'"><TD><A HREF="/forum/message.php?msg_id='.db_result($result, $i, 'msg_id').
				'"><IMG SRC="/images/msg.gif" BORDER=0 HEIGHT=12 WIDTH=10> ';
			/*
				See if this message is new or not
			*/
			if (get_forum_saved_date($forum_id) < db_result($result,$i,'date')) { $ret_val .= '<B>'; }

			$ret_val .= db_result($result, $i, 'subject') .'</A></TD>'.
				'<TD>'.db_result($result, $i, 'user_name').'</TD>'.
				'<TD>'.date($sys_datefmt,db_result($result,$i,'date')).'</TD></TR>';
			/*
				Show the body/message if requested
			*/
			if ($et == 1) {
				$ret_val .= '
				<TR BGCOLOR="'. util_get_alt_row_color($total_rows) .'"><TD>&nbsp;</TD><TD COLSPAN=2>'.
				nl2br(db_result($result, $i, 'body')).'</TD><TR>';
			}

			$ret_val .= show_submessages($thread_id,db_result($result, $i, 'msg_id'),1,$et);
		}
		$ret_val .= '</TABLE>';
	}
	return $ret_val;
}

function show_submessages($thread_id, $msg_id, $level,$et=0) {
	/*
		Recursive. Selects this message's id in this thread, 
		then checks if any messages are nested underneath it. 
		If there are, it calls itself, incrementing $level
		$level is used for indentation of the threads.
	*/
	global $total_rows,$sys_datefmt,$forum_id;

	$sql="SELECT user.user_name,forum.msg_id,forum.subject,forum.thread_id,forum.body,forum.date,forum.is_followup_to ".
		"FROM forum,user WHERE forum.thread_id='$thread_id' AND user.user_id=forum.posted_by AND forum.is_followup_to='$msg_id' ".
		"ORDER BY forum.date ASC, forum.subject ASC, forum.is_followup_to ASC;";

	$result=db_query($sql);
	$rows=db_numrows($result);

	if ($result && $rows > 0) {
		for ($i=0; $i<$rows; $i++) {
			/*
				Is this row's background shaded or not?
			*/
			$total_rows++;

			$ret_val .= '
				<TR BGCOLOR="'. util_get_alt_row_color($total_rows) .'"><TD NOWRAP>';
			/*
				How far should it indent?
			*/
			for ($i2=0; $i2<$level; $i2++) {
				$ret_val .= ' &nbsp; &nbsp; &nbsp; ';
			}

			$ret_val .= '<A HREF="/forum/message.php?msg_id='.db_result($result, $i, 'msg_id').
				'"><IMG SRC="/images/msg.gif" BORDER=0 HEIGHT=12 WIDTH=10> ';
			/*
				See if this message is new or not
			*/
			if (get_forum_saved_date($forum_id) < db_result($result,$i,'date')) { $ret_val .= '<B>'; }

			$ret_val .= db_result($result, $i, 'subject').'</A></TD>'.
				'<TD>'.db_result($result, $i, 'user_name').'</TD>'.
				'<TD>'.date($sys_datefmt,db_result($result,$i,'date')).'</TD></TR>';

			/*
				Show the body/message if requested
			*/
			if ($et == 1) {
				$ret_val .= '
					<TR BGCOLOR="'. util_get_alt_row_color($total_rows) .'"><TD>&nbsp;</TD><TD COLSPAN=2>'.
					nl2br(db_result($result, $i, 'body')).'</TD><TR>';
			}

			/*
				Call yourself, incrementing the level
			*/
			$ret_val .= show_submessages($thread_id,db_result($result, $i, 'msg_id'),($level+1),$et);
		}
	}
	return $ret_val;
}

function get_next_thread_id() {
	/*
		Get around limitation in MySQL - Must use a separate table with an auto-increment
	*/
	$result=db_query("INSERT INTO forum_thread_id VALUES ('')");

	if (!$result) {
		echo '<H1>Error!</H1>';
		echo db_error();
		exit;
	} else {
		return db_insertid($result);
	}
}

function get_forum_saved_date($forum_id) {
	/*
		return the save_date for this user
	*/
	global $forum_saved_date;

	if ($forum_saved_date) {
		return $forum_saved_date;
	} else {
		$sql="SELECT save_date FROM forum_saved_place WHERE user_id='".user_getid()."' AND forum_id='$forum_id';";
		$result = db_query($sql);
		if ($result && db_numrows($result) > 0) {
			$forum_saved_date=db_result($result,0,'save_date');
			return $forum_saved_date;
		} else {
			//highlight new messages from the past week only
			$forum_saved_date=(time()-604800);
			return $forum_saved_date;
		}
	}
}

function post_message($thread_id, $is_followup_to, $subject, $body, $group_forum_id) {
	global $feedback;
	if (user_isloggedin()) {
		if (!$group_forum_id) {
			exit_error('Error','Trying to post without a forum ID');
		}
		if (!$body || !$subject) {
			exit_error('Error','Must include a message body and subject');
		}
		if ($thread_id == 0) {
			$thread_id=get_next_thread_id();
		}

		$sql="INSERT INTO forum (group_forum_id,posted_by,subject,body,date,is_followup_to,thread_id) ".
			"VALUES ('$group_forum_id', '".user_getid()."', '".htmlspecialchars($subject)."', '".htmlspecialchars($body)."', '".time()."','$is_followup_to','$thread_id')";

		$result=db_query($sql);

		if (!$result) {
			echo "INSERT FAILED";
			echo db_error();
			$feedback .= ' Posting Failed ';
		} else {
			$feedback .= ' Message Posted ';
		}

		$msg_id=db_insertid($result);
		handle_monitoring($group_forum_id,$msg_id);

	} else {

		echo '
			<H3>You could post if you were logged in</H3>';

	}

}

function show_post_form($forum_id, $thread_id=0, $is_followup_to=0, $subject="") {

	global $PHP_SELF, $et;

	echo "<A NAME=\"_post\">";

	if (user_isloggedin()) {

		?>
		<CENTER>
		<FORM ACTION="<?php echo $PHP_SELF; ?>" METHOD="POST">
		<INPUT TYPE="HIDDEN" NAME="post_message" VALUE="y">
		<INPUT TYPE="HIDDEN" NAME="et" VALUE="<?php echo $et; ?>">
		<INPUT TYPE="HIDDEN" NAME="forum_id" VALUE="<?php echo $forum_id; ?>">
		<INPUT TYPE="HIDDEN" NAME="thread_id" VALUE="<?php echo $thread_id; ?>">
		<INPUT TYPE="HIDDEN" NAME="msg_id" VALUE="<?php echo $is_followup_to; ?>">
		<INPUT TYPE="HIDDEN" NAME="is_followup_to" VALUE="<?php echo $is_followup_to; ?>">
		<TABLE><TR><TD><B>Subject:</TD><TD>
		<INPUT TYPE="TEXT" NAME="subject" VALUE="<?php echo $subject; ?>" SIZE="15" MAXLENGTH="45">
		</TD></TR>
		<TR><TD><B>Message:</TD><TD>
		<TEXTAREA NAME="body" VALUE="" ROWS="10" COLS="70" WRAP="SOFT"></TEXTAREA>
		</TD></TR>
		<TR><TD COLSPAN="2" ALIGN="MIDDLE">
		<B><FONT COLOR="RED">HTML tags will display in your post as text</FONT></B>
		<BR>
		<INPUT TYPE="SUBMIT" NAME="SUBMIT" VALUE="Post Comment">
		</TD></TR></TABLE>
		</FORM>
		</CENTER>
		<?php

	} else {
		echo "<CENTER>";
		echo "\n\n<H3><FONT COLOR=\"RED\">You could post if you were logged in</FONT></H3>";
		echo "</CENTER>";
	}

}

function handle_monitoring($forum_id,$msg_id) {
	global $feedback;
	/*
		Checks to see if anyone is monitoring this forum
		If someone is, it sends them the message in email format
	*/

	$sql="SELECT user.email from forum_monitored_forums,user ".
		"WHERE forum_monitored_forums.user_id=user.user_id AND forum_monitored_forums.forum_id='$forum_id'";

	$result=db_query($sql);
	$rows=db_numrows($result);

	if ($result && $rows > 0) {
		$tolist=implode(result_column_to_array($result),', ');

		$sql="SELECT groups.unix_group_name,user.user_name,forum_group_list.forum_name,".
			"forum.group_forum_id,forum.thread_id,forum.subject,forum.date,forum.body ".
			"FROM forum,user,forum_group_list,groups ".
			"WHERE user.user_id=forum.posted_by ".
			"AND forum_group_list.group_forum_id=forum.group_forum_id ".
			"AND groups.group_id=forum_group_list.group_id ".
			"AND forum.msg_id='$msg_id'";

		$result = db_query ($sql);

		if ($result && db_numrows($result) > 0) {
			$body = "To: noreply@sourceforge.net".
				"\nBCC: $tolist".
				"\nSubject: [" .db_result($result,0,'unix_group_name'). " - " . db_result($result,0,'forum_name')."] " . 
					util_unconvert_htmlspecialchars(db_result($result,0,'subject')).
				"\n\nRead and respond to this message at: ".
				"\nhttp://sourceforge.net/forum/message.php?msg_id=".$msg_id.
				"\nBy: " . db_result($result,0, 'user_name') .
				"\n\n" . util_unconvert_htmlspecialchars(db_result($result,0, 'body')).
				"\n\n______________________________________________________________________".
				"\nYou are receiving this email because you elected to monitor this forum.".
				"\nTo stop monitoring this forum, login to SourceForge and visit: ".
				"\nhttp://sourceforge.net/forum/monitor.php?forum_id=$forum_id";

			exec ("/bin/echo \"". util_prep_string_for_sendmail($body) ."\" | /usr/sbin/sendmail -fnoreply@sourceforge.net -t &");

			$feedback .= ' email sent - people monitoring ';
		} else {
			$feedback .= ' email not sent - people monitoring ';
			echo db_error();
		}
	} else {
		$feedback .= ' email not sent - no one monitoring ';
		echo db_error();
	}
}

function recursive_delete($msg_id,$forum_id) {
	/*
		Take a message id and recurse, deleting all followups
	*/

	if ($msg_id=='' || $msg_id=='0' || (strlen($msg_id) < 1)) {
		return 0;
	}

	$sql="SELECT msg_id FROM forum WHERE is_followup_to='$msg_id' AND group_forum_id='$forum_id'";
	$result=db_query($sql);
	$rows=db_numrows($result);
	$count=1;

	for ($i=0;$i<$rows;$i++) {
		$count += recursive_delete(db_result($result,$i,'msg_id'),$forum_id);
	}
	$sql="DELETE FROM forum WHERE msg_id='$msg_id' AND group_forum_id='$forum_id'";
	$toss=db_query($sql);

	return $count;
}
?>
