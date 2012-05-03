<?
ini_set('memory_limit', -1);
//~~~~~~~~~~~ Main bookmarks page ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~//

authorize(true);

function compare($X, $Y){
	return($Y['count'] - $X['count']);
}

if(!empty($_GET['userid'])) {
	if(!check_perms('users_override_paranoia')) {
		error(403);
	}
	$UserID = $_GET['userid'];
	if(!is_number($UserID)) { error(404); }
	$DB->query("SELECT Username FROM users_main WHERE ID='$UserID'");
	list($Username) = $DB->next_record();
} else {
	$UserID = $LoggedUser['ID'];
}

$Sneaky = ($UserID != $LoggedUser['ID']);

$Data = $Cache->get_value('bookmarks_torrent_'.$UserID.'_full');

if($Data) {
	$Data = unserialize($Data);
	list($K, list($TorrentList, $CollageDataList)) = each($Data);
} else {
	// Build the data for the collage and the torrent list
	$DB->query("SELECT 
		bt.GroupID, 
		tg.WikiImage,
		tg.CategoryID,
		bt.Time
		FROM bookmarks_torrents AS bt
		JOIN torrents_group AS tg ON tg.ID=bt.GroupID
		WHERE bt.UserID='$UserID'
		ORDER BY bt.Time");
	
	$GroupIDs = $DB->collect('GroupID');
	$CollageDataList=$DB->to_array('GroupID', MYSQLI_ASSOC);
	if(count($GroupIDs)>0) {
		$TorrentList = get_groups($GroupIDs);
		$TorrentList = $TorrentList['matches'];
	} else {
		$TorrentList = array();
	}
}

$Title = ($Sneaky)?"$Username's bookmarked torrents":'Your bookmarked torrents';


// Loop through the result set, building up $Collage and $TorrentTable
// Then we print them.
$Collage = array();
$TorrentTable = '';

$NumGroups = 0;
$Artists = array();
$Tags = array();

foreach ($TorrentList as $GroupID=>$Group) {
	list($GroupID, $GroupName, $GroupYear, $GroupRecordLabel, $GroupCatalogueNumber, $TagList, $ReleaseType, $GroupVanityHouse, $Torrents, $GroupArtists) = array_values($Group);
	list($GroupID2, $Image, $GroupCategoryID, $AddedTime) = array_values($CollageDataList[$GroupID]);
	
	// Handle stats and stuff
	$NumGroups++;
	
	if($GroupArtists) {
		foreach($GroupArtists as $Artist) {
			if(!isset($Artists[$Artist['id']])) {
				$Artists[$Artist['id']] = array('name'=>$Artist['name'], 'count'=>1);
			} else {
				$Artists[$Artist['id']]['count']++;
			}
		}
	}
	
	$TagList = explode(' ',str_replace('_','.',$TagList));

	$TorrentTags = array();
	foreach($TagList as $Tag) {
		if(!isset($Tags[$Tag])) {
			$Tags[$Tag] = array('name'=>$Tag, 'count'=>1);
		} else {
			$Tags[$Tag]['count']++;
		}
		$TorrentTags[]='<a href="torrents.php?taglist='.$Tag.'">'.$Tag.'</a>';
	}
	$PrimaryTag = $TagList[0];
	$TorrentTags = implode(', ', $TorrentTags);
	$TorrentTags='<br /><div class="tags">'.$TorrentTags.'</div>';

	$DisplayName = '';
	if(count($GroupArtists)>0) {
		$DisplayName = display_artists(array('1'=>$GroupArtists));
	}
	$DisplayName .= '<a href="torrents.php?id='.$GroupID.'" title="View Torrent">'.$GroupName.'</a>';
	if($GroupYear>0) { $DisplayName = $DisplayName. ' ['. $GroupYear .']';}
	if($GroupVanityHouse) { $DisplayName .= ' [<abbr title="This is a vanity house release">VH</abbr>]'; }
	
	// Start an output buffer, so we can store this output in $TorrentTable
	ob_start(); 
        // Viewing a type that does not require grouping

        list($TorrentID, $Torrent) = each($Torrents);

        $DisplayName = '<a href="torrents.php?id='.$GroupID.'" title="View Torrent">'.$GroupName.'</a>';

        if(!empty($Torrent['FreeTorrent'])) {
                $DisplayName .=' <strong>Freeleech!</strong>'; 
        }

	$TorrentTable.=ob_get_clean();
	
	// Album art
	
	ob_start();
	
	$DisplayName = '';
	if(!empty($GroupArtists)) {
		$DisplayName.= display_artists(array('1'=>$GroupArtists), false);
	}
	$DisplayName .= $GroupName;
	if($GroupYear>0) { $DisplayName = $DisplayName. ' ['. $GroupYear .']';}
	$Collage[]=ob_get_clean();
	
}

uasort($Tags, 'compare');
$i = 0;
foreach ($Tags as $TagName => $Tag) {
	$i++;
	if($i>5) { break; }
uasort($Artists, 'compare');
$i = 0;
foreach ($Artists as $ID => $Artist) {
	$i++;
	if($i>10) { break; }
}
}

$JsonBookmarks = array();
foreach ($TorrentList as $Torrent) {
	$JsonTorrents = array();
	foreach ($Torrent['Torrents'] as $GroupTorrents) {
		$JsonTorrents[] = array(
			'id' => (int) $GroupTorrents['ID'],
			'groupId' => (int) $GroupTorrents['GroupID'],
			'media' => $GroupTorrents['Media'],
			'format' => $GroupTorrents['Format'],
			'encoding' => $GroupTorrents['Encoding'],
			'remasterYear' => (int) $GroupTorrents['RemasterYear'],
			'remastered' => $GroupTorrents['Remastered'] == 1,
			'remasterTitle' => $GroupTorrents['RemasterTitle'],
			'remasterRecordLabel' => $GroupTorrents['RemasterRecordLabel'],
			'remasterCatalogueNumber' => $GroupTorrents['RemasterCatalogueNumber'],
			'scene' => $GroupTorrents['Scene'] == 1,
			'hasLog' => $GroupTorrents['HasLog'] == 1,
			'hasCue' => $GroupTorrents['HasCue'] == 1,
			'logScore' => (float) $GroupTorrents['LogScore'],
			'fileCount' => (int) $GroupTorrents['FileCount'],
			'freeTorrent' => $GroupTorrents['FreeTorrent'] == 1,
			'size' => (float) $GroupTorrents['Size'],
			'leechers' => (int) $GroupTorrents['Leechers'],
			'seeders' => (int) $GroupTorrents['Seeders'],
			'snatched' => (int) $GroupTorrents['Snatched'],
			'time' => $GroupTorrents['Time'],
			'hasFile' => (int) $GroupTorrents['HasFile']
		);
	}
	$JsonBookmarks[] = array(
		'id' => (int) $Torrent['ID'],
		'name' => $Torrent['Name'],
		'year' => (int) $Torrent['Year'],
		'recordLabel' => $Torrent['RecordLabel'],
		'catalogueNumber' => $Torrent['CatalogueNumber'],
		'tagList' => $Torrent['TagList'],
		'releaseType' => $Torrent['ReleaseType'],
		'vanityHouse' => $Torrent['VanityHouse'] == 1,
		'torrents' => $JsonTorrents
	);
}

print
	json_encode(
		array(
			'status' => 'success',
			'response' => array(
				'bookmarks' => $JsonBookmarks
			)
		)
	);
?>