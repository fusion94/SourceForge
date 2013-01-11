<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: index.php,v 1.4 2000/01/13 18:36:36 precision Exp $

require ('vars.php');
require ('pre.php');
require ('../snippet/snippet_utils.php');

snippet_header(array('title'=>'Snippet Library'));

?>

<H2>Welcome to the Source Code Snippet Library</H2>
<P>
The purpose of this archive is to let you share your code snippets, scripts, 
and functions with the Open Source Software Community.
<P>
You can create a "new snippet", then post additional versions of that 
snippet quickly and easily.
<P>
Once you have snippets posted, you can then create a "Package" of snippets. 
That package can contain multiple, specific versions of other snippets.
<P>
<H3>Browse Snippets</H3>
<P>
You can browse the snippet library quickly:
<P>
<TABLE WIDTH="100%" BORDER="0">
<TR><TD>

</TD></TR>

<TR><TD>
<B>Browse by Language:</B>
<P>
<?php

$count=count($SCRIPT_LANGUAGE);
for ($i=1; $i<$count; $i++) {
	echo '
		<LI><A HREF="/snippet/browse.php?by=lang&lang='.$i.'">'.$SCRIPT_LANGUAGE[$i].'</A><BR>';
}

?>
</TD>
<TD>
<B>Browse by Category:</B>
<P>
<?php

$count=count($SCRIPT_CATEGORY);
for ($i=1; $i<$count; $i++) {
	echo '
		<LI><A HREF="/snippet/browse.php?by=cat&cat='.$i.'">'.$SCRIPT_CATEGORY[$i].'</A><BR>';
}

?>
</TD>
</TR>
</TABLE>

<?php
snippet_footer(array());

?>
