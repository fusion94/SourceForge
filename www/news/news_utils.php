<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: news_utils.php,v 1.60 2000/06/17 06:39:59 tperdue Exp $

/*
	News System
	By Tim Perdue, Sourceforge, 12/99
*/

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

function news_show_latest($group_id=714,$limit=10,$show_summaries=true) {
	global $sys_datefmt;
	/*
		Show a simple list of the latest news items with a link to the forum
	*/

	if ($group_id != 714) {
		$wclause="news_bytes.group_id='$group_id' AND news_bytes.is_approved <> '4'";
	} else {
		$wclause='news_bytes.is_approved=1';
	}

	$sql="SELECT groups.group_name,groups.unix_group_name,user.user_name,news_bytes.forum_id,news_bytes.summary,news_bytes.date,news_bytes.details ".
		"FROM user,news_bytes,groups ".
		"WHERE $wclause ".
		"AND user.user_id=news_bytes.submitted_by ".
		"AND news_bytes.group_id=groups.group_id ".
		"ORDER BY date DESC LIMIT $limit";

	$result=db_query($sql);
	$rows=db_numrows($result);

	if (!$result || $rows < 1) {
		$return .= '<H3>No News Items Found</H3>';
		$return .= db_error();
	} else {
		echo '
			<DL COMPACT>';
		for ($i=0; $i<$rows; $i++) {
			if ($show_summaries) {
				//get the first paragraph of the story
				$arr=explode("\n",db_result($result,$i,'details'));
				//if the first paragraph is short, and so are following paragraphs, add the next paragraph on
				if ((strlen($arr[0]) < 200) && (strlen($arr[1].$arr[2]) < 300) && (strlen($arr[2]) > 5)) {
					$summ_txt='<DD><FONT SIZE="-1">'. util_make_links( $arr[0].'<BR>'.$arr[1].'<BR>'.$arr[2] ).'</FONT></DD>';
				} else {
					$summ_txt='<DD><FONT SIZE="-1">'. util_make_links( $arr[0] ).'</FONT></DD>';
				}
				//show the project name 
				$proj_name=' &nbsp; - &nbsp; <A HREF="/projects/'. strtolower(db_result($result,$i,'unix_group_name')) .'/">'. db_result($result,$i,'group_name') .'</A>';
			} else {
				$proj_name='';
				$summ_txt='';
			}
			$return .= '
				<DT><A HREF="/forum/forum.php?forum_id='. db_result($result,$i,'forum_id') .'"><B>'. db_result($result,$i,'summary') . '</B></A>
				<BR><B><FONT SIZE="-1">'. db_result($result,$i,'user_name') .' - '.
					date($sys_datefmt,db_result($result,$i,'date')) . $proj_name . '</FONT></B>
				'. $summ_txt .'<BR>&nbsp;';
		}
		echo '
			</DL>';
	}
	if ($group_id != 714) {
		//you can only submit news from a project now
		//you used to be able to submit general news
		$return .= '<P><A HREF="/news/submit.php?group_id='.$group_id.'"><FONT SIZE="-1">[Submit News]</FONT></A><BR>&nbsp;';
	}
	return $return;
}

function news_portal_latest($group_id=0,$limit=5,$show_summaries=true) {
	global $sys_datefmt;
	/*
		Show a the latest news for a portal 
	*/

	$sql="SELECT groups.group_name,groups.unix_group_name,user.user_name,news_bytes.forum_id,news_bytes.summary,news_bytes.date,news_bytes.details ".
		"FROM user,news_bytes,groups,portal_news ".
		"WHERE portal_news.portal_id='$group_id' ".
		"AND user.user_id=news_bytes.submitted_by ".
		"AND portal_news.news_id=news_bytes.id ".
		"AND news_bytes.group_id=groups.group_id ".
		"ORDER BY date DESC LIMIT $limit";

	$result=db_query($sql);
	$rows=db_numrows($result);

	if (!$result || $rows < 1) {
		$return .= '<H3>No News Items Found</H3>';
		$return .= db_error();
	} else {
		echo '
			<DL COMPACT>';
		for ($i=0; $i<$rows; $i++) {
			if ($show_summaries) {
				//get the first paragraph of the story
				$arr=explode("\n",db_result($result,$i,'details'));
				if ((strlen($arr[0]) < 200) && (strlen($arr[1].$arr[2]) < 300) && (strlen($arr[2]) > 5)) {
					$summ_txt='<DD><FONT SIZE="-1">'. util_make_links( $arr[0].'<BR>'.$arr[1].'<BR>'.$arr[2] ).'</FONT></DD>';
				} else {
					$summ_txt='<DD><FONT SIZE="-1">'. util_make_links( $arr[0] ).'</FONT></DD>';
				}

				//show the project name
				$proj_name=' &nbsp; - &nbsp; <A HREF="/projects/'. strtolower(db_result($result,$i,'unix_group_name')) .'/">'. db_result($result,$i,'group_name') .'</A>';
			} else {
				$proj_name='';
				$summ_txt='';
			}
			$return .= '
				<DT><A HREF="/forum/forum.php?forum_id='. db_result($result,$i,'forum_id') .'"><B>'. db_result($result,$i,'summary') . '</B></A>
				<BR><B><FONT SIZE="-1">'. db_result($result,$i,'user_name') .' - '.
					date($sys_datefmt,db_result($result,$i,'date')) . $proj_name . '</FONT></B>
				'. $summ_txt .'<BR>&nbsp;';
		}
		echo '
			</DL>';
	}
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
