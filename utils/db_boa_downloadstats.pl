#!/usr/bin/perl
#
# $Id: db_boa_downloadstats.pl,v 1.15 2000/01/20 19:23:01 dtype Exp $
#
use DBI;

require("include.pl");  # Include all the predefined functions

unlink('/tmp/boa_stats.txt');
`wget -O /tmp/boa_stats.txt http://remission/hphd/boa_stats.txt`;
&db_connect;
$LOGFILE = '/tmp/boa_stats.txt';
open LOGFILE || die ('Cannot open /tmp/boa_stats.txt');

# for each release
while(<LOGFILE>) {
	$boastats = $_;
	if ($boastats =~ /^(F|G)::/) {
		if ($1 eq 'F') {
			chomp($boastats);
			@file = split('::',$boastats);
			#get group_id
			my $query = "SELECT group_id FROM groups WHERE unix_group_name='$file[1]'";
			my $res = $dbh->prepare($query);
			$res->execute;
			($group_id) = $res->fetchrow();
			#update db
			$query = "UPDATE filerelease SET downloads='$file[3]',"
				."downloads_week='$file[4]' WHERE group_id='$group_id' "
				."AND filename='$file[2]'";
			$res = $dbh->prepare($query);
			$res->execute;
		} elsif ($1 eq 'G') {
			chomp($boastats);
			@group = split('::',$boastats);
			#update db
			$query = "UPDATE groups SET file_downloads='$group[2]' "
				."WHERE unix_group_name='$group[1]'";
			$res = $dbh->prepare($query);
			$res->execute;
		}
	}
}

#	my $query = "SELECT count(*) FROM filedownload_log WHERE filerelease_id=$filerelease_id";
#	my $count = $dbh->prepare($query);
#	$count->execute();
#	($row_count) = $count->fetchrow();

	# and for weekly
#	my $query = "SELECT count(*) FROM filedownload_log WHERE filerelease_id=$filerelease_id "
#		. "AND time>" . (time()-(7*24*3600));
#	my $count = $dbh->prepare($query);
#	$count->execute();
#	($row_count_week) = $count->fetchrow();
	

#	print "ID $filerelease_id: $row_count downloads\n";
	
# only update if count is different
#	if (($row_count != $oldcount) || ($row_count_week != $oldcount_week)) { 
#		$query = "UPDATE filerelease SET downloads=$row_count,downloads_week=$row_count_week "
#			." WHERE filerelease_id=$filerelease_id";
#		my $update = $dbh->prepare($query);
#		$update->execute();
#	}
#}
