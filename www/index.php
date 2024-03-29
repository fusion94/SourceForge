<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: index.php,v 1.217 2000/12/12 21:16:57 pfalcon Exp $

require ('pre.php');    // Initial db and session library, opens session
require ('cache.php');
require($DOCUMENT_ROOT.'/forum/forum_utils.php');
require ('features_boxes.php');

$HTML->header(array('title'=>'Welcome'));

?>
<!-- whole page table -->
<TABLE width=100% cellpadding=5 cellspacing=0 border=0>
<TR><TD width="65%" VALIGN="TOP">

	<hr width="100%" size="1" noshade>
	<span class="slogan">
	<div align="center">
	<?php echo $Language->BREAKING_DOWN_BARRIERS; ?>
	</div>
	</span>
        <hr width="100%" size="1" noshade>
	&nbsp;<br>
<P>
<?php

/*

	Temp way of getting

	blurb before the content mgr is ready

*/

echo $Language->HOME_PAGE_ABOUT_BLURB;
echo '<P>';
// echo $HTML->box1_top($Language->GROUP_LONG_FOUNDRIES);
?>

<br><b>SourceForge Development Foundries</b><br><br>
<table bgcolor="White" border="0" cellpadding="0" cellspacing="0" valign="top" width="100%">
<tr>
	<td>Hardware:</td>
	<td>Programming:</td>
</tr>
<tr>
	<td><font size="-1"><a href="/foundry/printing/">Printing</a>, <a href="/foundry/storage/">Storage</a></font></td>
	<td><font size="-1"><a href="/foundry/java/">Java</a>, <a href="/foundry/perl-foundry/">Perl</a>, <a href="/foundry/php-foundry/">PHP</a>, <a href="/foundry/python-foundry/">Python</a>, <a href="/foundry/tcl-foundry/">Tcl/Tk</a>, <a href="/foundry/gnome-foundry/">GNOME</a></font></td>
</tr>
<tr>
	<td>International:</td>
	<td>Services:</td>
</tr>
<tr>
	<td><font size="-1"><a href="/foundry/french/">French</a>, <a href="/foundry/spanish/">Espanol</a>, <a href="/foundry/japanese/">Japanese</a></font></td>
	<td><font size="-1"><a href="/foundry/databases/">Database</a>, <a href="/foundry/web/">Web</a></font></td>
</tr>
<tr>
	<td>Graphics:</td>
	<td>Fun:</td>
</tr>
<tr>
	<td><font size="-1"><a href="/foundry/vectorgraphics/">Vector Graphics</a>, <a href="/foundry/3d/">3D</a></font></td>
	<td><font size="-1"><a href="/foundry/games/">Games</a></font></td>
</tr>
<tr>
		<td>&nbsp;</td><td align="right"><font size="-1"><a href="about_foundries.php">[ More ]</a></font></td>
</tr>
</table>
<br>

<?php
echo $HTML->box1_top($Language->GROUP_LONG_NEWS);
echo news_show_latest($sys_news_group,5,true,false,false,5);
echo $HTML->box1_bottom();
?>

</TD>

<?php

echo '<TD width="35%" VALIGN="TOP">';

echo cache_display('show_features_boxes','show_features_boxes()',3600);

?>

</TD></TR></TABLE>

<?php

$HTML->footer(array());

?>
