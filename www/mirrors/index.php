<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: index.php,v 1.13 2000/01/16 02:03:31 dtype Exp $

require "pre.php";    
site_header(array(title=>"Mirrors of Other Sites"));
?>

<P>SourceForge provides high-bandwidth mirrors for several other
projects. Our mirror server is a Quad XEON 400Mhz, with 2 GB RAM
and 850 GB of formatted storage on 5 Mylex ExtremeRAID controllers. Its
switched 100Mbit connection feeds directly to VA routers and two
DS-3 lines.

<P>Following is a partial mirror list. All mirrors can be found at:
<UL><LI><B><A href="http://download.sourceforge.net/mirrors/">http://download.sourceforge.net/mirrors/</A></B>
(preferred)
<LI><B><A href="ftp://download.sourceforge.net/pub/mirrors/">ftp://download.sourceforge.net/pub/mirrors/</A></B>
</UL>

<HR>

<P><B><A href="http://download.sourceforge.net/mirrors/CPAN/">CPAN</A></B> -
CPAN is the Comprehensive Perl Archive Network. Here you will find All
Things Perl. <I>(Mirror: rsync from ftp.funet.fi)</I>

<P><?php html_image('others/gnome1.png',array(align=>'right')); ?>
<B><A href="http://download.sourceforge.net/mirrors/gnome/">gnome</A></B> - GNOME 
is the GNU Network Object Model Environment. The GNOME project intends 
to build a complete, easy-to-use desktop
environment for the user, and a powerful application framework 
for the software developer. <I>(Mirror: ftp.gnome.org/pub/GNOME/)</I>

<P><?php html_image('others/kde-logotp3.png',array(align=>'right')); ?>
<B><A href="http://download.sourceforge.net/mirrors/kde/">kde</A></B> -
KDE is a powerful graphical desktop environment for Unix workstations. It combines
ease of use, contemporary functionality and outstanding graphical design with the
technological superiority of the Unix operating system.  
<I>(Mirror: ftp.kde.org/pub/kde/)</I>

<P><B><A href="http://download.sourceforge.net/mirrors/kernel.org/">kernel.org</A></B> -
The Linux Kernel Archives is the primary site for the Linux kernel source.
<I>(Mirror: ftp.kernel.org/pub/linux/kernel/)</I>

<?php
site_footer(array());
site_cleanup(array());
?>
