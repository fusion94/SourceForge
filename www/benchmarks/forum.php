<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: forum.php,v 1.23 2000/07/06 17:35:10 tperdue Exp $

$sys_dbhost="localhost";
$sys_dbname="benchmark_db";
//MYSQL
//$sys_dbuser="www";
//POSTGRES
$sys_dbuser="tperdue";

$sys_dbpasswd="tony";

require($DOCUMENT_ROOT.'/benchmarks/database.php');
require($DOCUMENT_ROOT.'/benchmarks/utils.php');

db_connect('');
if (!$conn) {
	echo db_error();
//	echo pg_errormessage();
}

$sys_datefmt="Y-M-D H:i";


/*
	one out of every 10 pages inserts a record into the forum database
* /

srand((double)microtime()*1000000);
$random_num=rand(0,10000);

//echo $random_num;

if ($random_num > 9000) {
	//insert a record

	$res=db_query("INSERT INTO forum (group_forum_id,posted_by,subject,date,is_followup_to,thread_id,has_followups) ".
		"VALUES ('1','55','AUTO INSERT','".time()."','95550','95550','0')");
	if (!$res || db_affected_rows($res) < 1) {
		echo 'ERROR INSERTING RECORD '.db_error();
//	} else {
//		echo 'INSERTED SUCCESSFULLY';
	}
} else {
//	echo $random_num;
}
*/

function forum_show_a_nested_message ($result,$row=0) {
	/*

		accepts a database result handle to display a single message
		in the format appropriate for the nested messages

		second param is which row in that result set to use

	*/
	global $sys_datefmt;

	$ret_val = '
		<TABLE BORDER="0">
			<TR>
				<TD BGCOLOR="#DDDDDD" NOWRAP>By: <A HREF="/developer/?form_dev='.
					db_result($result, $row, 'user_id') .'">'. 
					db_result($result, $row, 'user_name') .'</A>'.
					' ( ' .db_result($result, $row, 'realname') . ' ) '.
					'<BR><A HREF="/forum/message.php?msg_id='.
					db_result($result, $row, 'msg_id') .'">'.
					'<IMG SRC="/images/msg.gif" BORDER=0 HEIGHT=12 WIDTH=10> '.
					db_result($result, $row, 'subject') .' [ reply ]</A> &nbsp; '.
					'<BR>'. date($sys_datefmt,db_result($result,$row,'date')) .'
				</TD>
			</TR>
			<TR>
				<TD>
				</TD>
			</TR>
		</TABLE>';
	return $ret_val;
}

function forum_show_nested_messages ($thread_id, $msg_id) {
	global $total_rows,$sys_datefmt;

	$sql="SELECT users.user_name,forum.has_followups,users.realname,users.user_id,forum.msg_id,forum.subject,forum.thread_id,forum.date,forum.is_followup_to ".
		"FROM forum,users WHERE forum.thread_id='$thread_id' AND users.user_id=forum.posted_by AND forum.is_followup_to='$msg_id' ".
		"ORDER BY forum.date ASC, forum.subject ASC, forum.is_followup_to ASC;";

	$result=db_query($sql);
	$rows=db_numrows($result);

	$ret_val='';

	if ($result && $rows > 0) {
		$ret_val .= '
			<UL>';

		/*

			iterate and show the messages in this result

			for each message, recurse to show any submessages

		*/
		for ($i=0; $i<$rows; $i++) {
			//	increment the global total count
			$total_rows++;

			//	show the actual nested message
			$ret_val .= forum_show_a_nested_message ($result,$i).'<P>';
			if (db_result($result,$i,'has_followups') > 0) {
				//	Call yourself if there are followups
				$ret_val .= forum_show_nested_messages ( $thread_id, db_result($result,$i,'msg_id') );
			}
		}
		$ret_val .= '
			</UL>';
	}

	return $ret_val;
}

if (!$forum_id) {
	$forum_id=1;
}

	/*
		set up some defaults if they aren't provided
	*/
	if ((!$offset) || ($offset < 0)) {
		$offset=0;
	} 

	if (!$style) {
		$style='nested';
	}

	if (!$max_rows || $max_rows < 5) {
		$max_rows=50;
	}

//now set up the query
	if ($style == 'nested' || $style== 'threaded' ) {
		//the flat and 'no comments' view just selects the most recent messages out of the forum
		//the other views just want the top message in a thread so they can recurse.
		$threading_sql='AND forum.is_followup_to=0';
	}

	$sql="SELECT users.user_name,users.realname,forum.has_followups,users.user_id,forum.msg_id,forum.subject,forum.thread_id,forum.date,forum.is_followup_to ".
		"FROM forum,users WHERE forum.group_forum_id='$forum_id' AND users.user_id=forum.posted_by $threading_sql ".
// MYSQL
//		"ORDER BY forum.date DESC, forum.subject ASC, forum.is_followup_to ASC LIMIT $offset,".($max_rows+1);
//POSTGRES
		"ORDER BY forum.date DESC, forum.subject ASC, forum.is_followup_to ASC LIMIT ".($max_rows+1)." OFFSET $offset";

	$result=db_query($sql);
	$rows=db_numrows($result);

	if ($rows > $max_rows) {
		$rows=$max_rows;
	}

	$total_rows=0;

	if (!$result || $rows < 1) {
		//empty forum
		$ret_val .= 'No Messages in '.$forum_name .'<P>'. db_error();
//		echo pg_errormessage($conn);
	} else {

		/*

			build table header

		*/

	//create a pop-up select box listing the forums for this project
		$public_flag='0,1';

		$res=db_query("SELECT group_forum_id,forum_name ".
				"FROM forum_group_list ".
				"WHERE group_id='$group_id' AND is_public IN ($public_flag)");
		$vals=util_result_column_to_array($res,0);
		$texts=util_result_column_to_array($res,1);

		$forum_popup = util_build_select_box_from_arrays ($vals,$texts,'forum_id',$forum_id,false);

	//create a pop-up select box showing options for viewing threads

		$vals=array('nested','flat','threaded','nocomments');
		$texts=array('Nested','Flat','Threaded','No Comments');

		$options_popup=util_build_select_box_from_arrays ($vals,$texts,'style',$style,false);

	//create a pop-up select box showing options for max_row count
		$vals=array(25,50,75,100);
		$texts=array('Show 25','Show 50','Show 75','Show 100');

		$max_row_popup=util_build_select_box_from_arrays ($vals,$texts,'max_rows',$max_rows,false);

	//now show the popup boxes in a form
		$ret_val .= '<TABLE BORDER="0" WIDTH="50%">
				<FORM ACTION="'. $PHP_SELF .'" METHOD="POST">
				<INPUT TYPE="HIDDEN" NAME="set" VALUE="custom">
				<TR><TD><FONT SIZE="-1">'. $forum_popup .
					'</TD><TD><FONT SIZE="-1">'. $options_popup .
					'</TD><TD><FONT SIZE="-1">'. $max_row_popup .
					'</TD><TD><FONT SIZE="-1"><INPUT TYPE="SUBMIT" NAME="SUBMIT" VALUE="Change View"></TD></TR></TABLE></FORM>';

		if ($style == 'nested') {
			/*
				no top table row for nested threads
			*/
		} else {
			/*
				threaded, no comments, or flat display

				different header for default threading and flat now
			*/
			$ret_val .= '<TABLE WIDTH="100%" CELLPADDING="2" CELLSPACING="0" BGCOLOR="#FFFFFF" BORDER="0">
				<TR BGCOLOR="'.$GLOBALS['COLOR_MENUBARBACK'].'">
				<TD WIDTH="25%"><FONT COLOR=#FFFFFF><B>Thread/Subject</TD>
				<TD><FONT COLOR=#FFFFFF><B>Author</TD>
				<TD><FONT COLOR="#FFFFFF"><B>Date/Time</TD></TR>';
		}

		$i=0;
		while (($total_rows < $max_rows) && ($i < $rows)) {
			$total_rows++;
			if ($style == 'nested') {
				/*
					New slashdot-inspired nested threads,
					showing all submessages and bodies
				*/
				//show this one message
				$ret_val .= forum_show_a_nested_message ( $result,$i ).'<BR>';

				if (db_result($result,$i,'has_followups') > 0) {
					//show submessages for this message
					$ret_val .= forum_show_nested_messages ( db_result($result,$i,'thread_id'), db_result($result,$i,'msg_id') );
				}
			} else if ($style == 'flat') {

				//just show the message boxes one after another

				$ret_val .= forum_show_a_nested_message ( $result,$i ).'<BR>';

			} else {
				/*
					no-comments or threaded use the "old" colored-row style

					phorum-esque threaded list of messages,
					not showing message bodies
				*/

				$ret_val .= '
					<TR BGCOLOR="'. util_get_alt_row_color($total_rows) .'"><TD><A HREF="/forum/message.php?msg_id='.
					db_result($result, $i, 'msg_id').'">'.
					'<IMG SRC="/images/msg.gif" BORDER=0 HEIGHT=12 WIDTH=10> ';
				/*

					See if this message is new or not
					If so, highlite it in bold

				*/
				if (get_forum_saved_date($forum_id) < db_result($result,$i,'date')) {
					$ret_val .= '<B>';
				}
				/*
					show the subject and poster
				*/
				$ret_val .= db_result($result, $i, 'subject').'</A></TD>'.
					'<TD>'.db_result($result, $i, 'user_name').'</TD>'.
					'<TD>'.date($sys_datefmt,db_result($result,$i,'date')).'</TD></TR>';

				/*

					Show subjects for submessages in this thread

					show_submessages() is recursive

				*/
				if ($style == 'threaded') {
					if (db_result($result,$i,'has_followups') > 0) {
						$ret_val .= show_submessages(db_result($result, $i, 'thread_id'),
							db_result($result, $i, 'msg_id'),1,0);
					}
				}
			}

			$i++;
		}

		/*
			This code puts the nice next/prev.
		*/
		if ($style=='nested' || $style=='flat') {
			$ret_val .= '<TABLE WIDTH="100%" BORDER="0">';
		}
		$ret_val .= '
				<TR BGCOLOR="#EEEEEE"><TD WIDTH="50%">';
		if ($offset != 0) {
			$ret_val .= '<FONT face="Arial, Helvetica" SIZE="3" STYLE="text-decoration: none"><B>
				<A HREF="javascript:history.back()">
				<B><IMG SRC="/images/t2.gif" HEIGHT=15 WIDTH=15 BORDER=0 ALIGN=MIDDLE> Previous Messages</A></B></FONT>';
		} else {
			$ret_val .= '&nbsp;';
		}

		$ret_val .= '</TD><TD>&nbsp;</TD><TD ALIGN="RIGHT" WIDTH="50%">';
		if (db_numrows($result) > $i) {
			$ret_val .= '<FONT face="Arial, Helvetica" SIZE=3 STYLE="text-decoration: none"><B>
				<A HREF="/forum/forum.php?max_rows='.$max_rows.'&style='.$style.'&offset='.($offset+$i).'&forum_id='.$forum_id.'">
				<B>Next Messages <IMG SRC="/images/t.gif" HEIGHT=15 WIDTH=15 BORDER=0 ALIGN=MIDDLE></A>';
		} else {
			$ret_val .= '&nbsp;';
		}

		$ret_val .= '</TABLE>';
	}

	echo $ret_val;

?>
