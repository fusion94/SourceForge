#!/usr/bin/perl
#
# $Id: db_downloadstats.pl,v 1.4 1999/11/16 21:53:30 dtype Exp $
#
use DBI;

require("include.pl");  # Include all the predefined functions

&db_connect;

# gather all releases 
my $query = "SELECT filerelease_id,downloads,downloads_week FROM filerelease";
my $rel = $dbh->prepare($query);
$rel->execute();

# for each release
while(my ($filerelease_id,$oldcount,$oldcount_week) = $rel->fetchrow()) {
	my $query = "SELECT count(*) FROM filedownload_log WHERE filerelease_id=$filerelease_id";
	my $count = $dbh->prepare($query);
	$count->execute();
	($row_count) = $count->fetchrow();

	# and for weekly
	my $query = "SELECT count(*) FROM filedownload_log WHERE filerelease_id=$filerelease_id "
		. "AND time>" . (time()-(7*24*3600));
	my $count = $dbh->prepare($query);
	$count->execute();
	($row_count_week) = $count->fetchrow();
	

	print "ID $filerelease_id: $row_count downloads\n";
	
# only update if count is different
	if (($row_count != $oldcount) || ($row_count_week != $oldcount_week)) { 
		$query = "UPDATE filerelease SET downloads=$row_count,downloads_week=$row_count_week "
			." WHERE filerelease_id=$filerelease_id";
		my $update = $dbh->prepare($query);
		$update->execute();
	}
}
