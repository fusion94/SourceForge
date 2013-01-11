<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: contact.php,v 1.7 2000/01/13 18:36:34 precision Exp $

require "pre.php";    // Initial db and session library, opens session
site_header(array('title'=>'Contact Us'));
?>

<P>You may contact any of the <A href="staff.php">staff</A> directly
via email, or the general bunch via <A href="/sendmessage.php?toaddress=admin_maillink_sourceforge.net">Contact Form</A>.
<P>
If you feel you are encountering a bug or unusual error of any kind, 
please <A HREF="/bugs/?func=addbug&group_id=1"><B>submit a bug</B></A>.
<P>
If you need to contact us by phone, call VA Linux Systems
at 888-LINUX-4U and tell them you wish to speak to someone on
the SourceForge crew. When the operator acts confused, explain that
this is the group that lives in the dark room with all the computers
and a locked door.

<P>All press inquiries should be directed to:
<UL><LI><A href="/sendmessage.php?toaddress=eureka_maillink_valinux.com">Eureka Endo</A>
<BR>Press Relations Manager, VA Linux Systems
<BR>408.542.5754</UL>

<P>All complaints/bugs should be directed to:
<UL><LI><A href="/bugs/?group_id=1">Our Bug Tracking System</A></UL>
<P>If you're especially bored, you can visit our bug tracking system
to see what other people have submitted, too.

<?php
site_footer(array());
site_cleanup(array());
?>
