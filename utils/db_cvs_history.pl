#!/usr/bin/perl
#
# $Id: db_cvs_history.pl,v 1.3 2000/02/07 15:28:29 dtype Exp $
#
use DBI;

require("include.pl");  # Include all the predefined functions

unlink('/tmp/cvs_history.txt');
`wget -q -O /tmp/cvs_history.txt http://cvs1/cvs_history_parse.txt`;
&db_connect;
$LOGFILE = '/tmp/cvs_history.txt';
open(LOGFILE) || die ('Cannot open /tmp/boa_stats.txt');

# for each release
while(<LOGFILE>) {
	$cvshist = $_;
	if ($cvshist =~ /^(C|G)::/) {
		if ($1 eq 'G') {
			chomp($cvshist);
			@group = split('::',$cvshist);
			#get group_id
			my $query = "SELECT group_id FROM groups WHERE unix_group_name='$group[1]'";
			my $res = $dbh->prepare($query);
			$res->execute;
			($group_id) = $res->fetchrow();
		} elsif ($1 eq 'C') {
			chomp($cvshist);
			@user = split('::',$cvshist);
			#update db
			$query = "DELETE FROM group_cvs_history WHERE group_id='$group_id' "
				."AND user_name='$user[1]'";
			$res = $dbh->prepare($query);
			$res->execute;
			$query = "INSERT INTO group_cvs_history (group_id,user_name,cvs_commits,"
				."cvs_commits_wk,cvs_adds,cvs_adds_wk) VALUES ("
				."$group_id,'$user[1]','$user[2]','$user[3]','$user[4]','$user[5]')";
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
