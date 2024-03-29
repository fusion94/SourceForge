<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: news_utils.php,v 1.81 2000/12/13 11:22:28 pfalcon Exp $

/*
	News System
	By Tim Perdue, Sourceforge, 12/99
*/

function news_header($params) {
	global $DOCUMENT_ROOT,$HTML,$group_id,$news_name,$news_id,$sys_news_group;

	$params['toptab']='news';
	$params['group']=$group_id;

	/*
		Show horizontal links
	*/
	if ($group_id && ($group_id != $sys_news_group)) {
		site_project_header($params);
	} else {
		$HTML->header($params);
		echo '
			<H2>SourceForge <A HREF="/news/">News</A></H2>';
	}
	echo '<P><B>';
	echo '<A HREF="/news/submit.php?group_id='.$group_id.'">Submit</A> | <A HREF="/news/admin/?group_id='.$group_id.'">Admin</A></B>';
	echo '<P>';
}

function news_footer($params) {
	GLOBAL $HTML;
	$HTML->footer($params);
}

function news_show_latest($group_id='',$limit=10,$show_summaries=true,$allow_submit=true,$flat=false,$tail_headlines=0) {
	global $sys_datefmt,$sys_news_group;
	if (!$group_id) {
		$group_id=$sys_news_group;
	}
	/*
		Show a simple list of the latest news items with a link to the forum
	*/

	if ($group_id != $sys_news_group) {
		$wclause="news_bytes.group_id='$group_id' AND news_bytes.is_approved <> '4'";
	} else {
		$wclause='news_bytes.is_approved=1';
	}

	$sql="SELECT groups.group_name,groups.unix_group_name,groups.type,users.user_name,news_bytes.forum_id,news_bytes.summary,news_bytes.date,news_bytes.details ".
		"FROM users,news_bytes,groups ".
		"WHERE $wclause ".
		"AND users.user_id=news_bytes.submitted_by ".
		"AND news_bytes.group_id=groups.group_id ".
		"ORDER BY date DESC";

	$result=db_query($sql,$limit+$tail_headlines);
	$rows=db_numrows($result);

	if (!$result || $rows < 1) {
		$return .= '<H3>No News Items Found</H3>';
		$return .= db_error();
	} else {
		echo '
			<DL COMPACT>';
		for ($i=0; $i<$rows; $i++) {
			if ($show_summaries && $limit) {
				//get the first paragraph of the story
				$arr=explode("\n",db_result($result,$i,'details'));
				//if the first paragraph is short, and so are following paragraphs, add the next paragraph on
				if ((strlen($arr[0]) < 200) && (strlen($arr[1].$arr[2]) < 300) && (strlen($arr[2]) > 5)) {
					$summ_txt='<BR>'. util_make_links( $arr[0].'<BR>'.$arr[1].'<BR>'.$arr[2] );
				} else {
					$summ_txt='<BR>'. util_make_links( $arr[0] );
				}
				//show the project name 
				if (db_result($result,$i,'type')==2) $group_type='/foundry/';
				else $group_type='/projects/';
				$proj_name=' &nbsp; - &nbsp; <A HREF="http://'.$GLOBALS['sys_default_domain'].$group_type. strtolower(db_result($result,$i,'unix_group_name')) .'/">'. db_result($result,$i,'group_name') .'</A>';
			} else {
				$proj_name='';
				$summ_txt='';
			}

                        if (!$limit) {
				$return .= '
					<li><A HREF="http://'.$GLOBALS['sys_default_domain'].'/forum/forum.php?forum_id='. db_result($result,$i,'forum_id') .'"><B>'. db_result($result,$i,'summary') . '</B></A>';
                        	$return .= ' &nbsp; <I>'.date($sys_datefmt,db_result($result,$i,'date')).'</A></I><br>';
                        }
                        else {
				$return .= '
					<A HREF="http://'.$GLOBALS['sys_default_domain'].'/forum/forum.php?forum_id='. db_result($result,$i,'forum_id') .'"><B>'. db_result($result,$i,'summary') . '</B></A>';
                        	if (!$flat)
                        		$return .= '
					<BR>&nbsp;';
                        	$return .= '
                                &nbsp;&nbsp;&nbsp;<I>'. db_result($result,$i,'user_name') .' - '.
					date($sys_datefmt,db_result($result,$i,'date')) .' <A HREF="/projects/'. strtolower(db_result($result,$i,'unix_group_name')) .'">'. $proj_name . '</A></I>
				'. $summ_txt .'<HR width="100%" size="1" noshade>';
			}

                        if ($limit==1 && $tail_headlines) $return .= "<ul>";
                	if ($limit) $limit--;
		}
	}

	if ($group_id != $sys_news_group)
          $archive_url='/news/?group_id='.$group_id;
        else
          $archive_url='/news/';

        if ($tail_headlines) $return .= '</ul><HR width="100%" size="1" noshade>'."\n";

        $return .= '<div align="center"><a href="'.$archive_url.'">[News archive]</a></div>';
	if ($allow_submit && $group_id != $sys_news_group) {
		//you can only submit news from a project now
		//you used to be able to submit general news
		$return .= '<div align="center"><A HREF="/news/submit.php?group_id='.$group_id.'"><FONT SIZE="-1">[Submit News]</FONT></A></center>';
	}
	return $return;
}

function news_foundry_latest($group_id=0,$limit=5,$show_summaries=true) {
	global $sys_datefmt;
	/*
		Show a the latest news for a portal 
	*/

	$sql="SELECT groups.group_name,groups.unix_group_name,users.user_name,news_bytes.forum_id,news_bytes.summary,news_bytes.date,news_bytes.details ".
		"FROM users,news_bytes,groups,foundry_news ".
		"WHERE foundry_news.foundry_id='$group_id' ".
		"AND users.user_id=news_bytes.submitted_by ".
		"AND foundry_news.news_id=news_bytes.id ".
		"AND news_bytes.group_id=groups.group_id ".
		"AND foundry_news.is_approved=1 ".
		"ORDER BY news_bytes.date DESC";

	$result=db_query($sql,$limit);
	$rows=db_numrows($result);

	if (!$result || $rows < 1) {
		$return .= '<H3>No News Items Found</H3>';
		$return .= db_error();
	} else {
		for ($i=0; $i<$rows; $i++) {
			if ($show_summaries) {
				//get the first paragraph of the story
				$arr=explode("\n",db_result($result,$i,'details'));
				if ((strlen($arr[0]) < 200) && (strlen($arr[1].$arr[2]) < 300) && (strlen($arr[2]) > 5)) {
					$summ_txt=util_make_links( $arr[0].'<BR>'.$arr[1].'<BR>'.$arr[2] );
				} else {
					$summ_txt=util_make_links( $arr[0] );
				}

				//show the project name
				$proj_name=' &nbsp; - &nbsp; <A HREF="/projects/'. strtolower(db_result($result,$i,'unix_group_name')) .'/">'. db_result($result,$i,'group_name') .'</A>';
			} else {
				$proj_name='';
				$summ_txt='';
			}
			$return .= '
				<A HREF="/forum/forum.php?forum_id='. db_result($result,$i,'forum_id') .'"><B>'. db_result($result,$i,'summary') . '</B></A>
				<BR><I>'. db_result($result,$i,'user_name') .' - '.
					date($sys_datefmt,db_result($result,$i,'date')) . $proj_name . '</I>
				'. $summ_txt .'<HR WIDTH="100%" SIZE="1">';
		}
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
