<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: calendar.php,v 1.3 2000/05/17 21:51:55 tperdue Exp $

require "pre.php";    // Initial db and session library, opens session

site_header(array(title=>"Project Management: Add Task"));

$timedate = time();

?> 
<p>&nbsp;</p>

<table border="0" width="800">
<tr>  
	<td width="800">
	<pre>
<?php
	$cal = `/usr/bin/cal -y`;
	print ("$cal");
?>
	</pre>
	</td>

</tr>

</table>

<?php
site_footer(array());

?>
