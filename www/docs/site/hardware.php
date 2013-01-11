<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: hardware.php,v 1.26 2000/01/13 18:36:35 precision Exp $

require "pre.php";    
site_header(array(title=>"Welcome to Project Alexandria"));
?>

<P><H1>SourceForge Hardware Summary</H1></P>

<P>
<TABLE>
<TR valign=top>
<TD><B>Load Balancing Firewall Server</B></TD>
<TD>PII 350<BR>128MB RAM<BR> 4.5GB U2W SCSI HDD</TD>
</TR>

<TR valign=top>
<TD><B>SourceForge Web Server 1</B></TD>
<TD>Dual PIII 600<BR>512MB RAM<BR> 18GB U2W SCSI HDD</TD>
</TR>

<TR valign=top>
<TD><B>SourceForge Web Server 2</B></TD>
<TD>Dual PIII 600<BR>512MB RAM<BR> 18GB U2W SCSI HDD</TD>
</TR>

<TR valign=top>
<TD><B>SourceForge Database Server</B></TD>
<TD>Quad PII-Xeon 400<BR>2GB RAM<BR>75GB RAID-5</TD>
</TR>

<TR valign=top>
<TD><B>SourceForge File Server</B></TD>
<TD>Quad PII-Xeon 400<BR>2GB RAM<BR>850GB on 5 Mylex ExtremeRaid Controllers</TD>
</TR>

<TR valign=top>
<TD><B>SourceForge Mail/DNS Server</B></TD>
<TD>Dual PII-350<BR>512MB RAM<BR>54GB U2W SCSI HDD</TD>
</TR>

<TR valign=top>
<TD><B>Geocrawler Mail Archiver</B></TD>
<TD>Dual PIII-500<BR>1GB RAM<BR>154GB RAID-5</TD>
</TR>

<TR valign=top>
<TD><B>Sitewide Backup Server</B></TD>
<TD>PII-350<BR>128MB RAM<BR>18GB U2W SCSI HDD<BR>5 Seagate 25GB Native AIT Tape Drives</TD>
</TR>

<TR valign=top>
<TD><B>Project CVS Server</B></TD>
<TD>Dual PIII-600<BR>1GB RAM<BR>75GB RAID-5</TD>
</TR>

<TR valign=top>
<TD><B>Project Web Server</B></TD>
<TD>Dual PIII-600<BR>512MB RAM<BR>2 18GB U2W SCSI HDD</TD>
</TR>

<TR valign=top>
<TD><B>Project Database Server</B></TD>
<TD>Dual PIII-Xeon 500<BR>1GB RAM<BR>35GB RAID-5</TD>
</TR>

<TR valign=top>
<TD><B>Totals (Rough)</B></TD>
<TD>CPU: 11.4Gz<BR>RAM: 9.5GB<BR>Storage: 1.34TB (Formatted Storage, After RAID)</TD>
</TR>

</TABLE>


<?php
site_footer(array());
site_cleanup(array());
?>
