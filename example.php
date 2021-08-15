<?php
require_once('vendor/autoload.php');

use Corbpie\YouTubeApiClass\YTAPI;

$call = new YTAPI();

//header('Content-Type: application/json');

$call->setChannelId('UCa10nxShhzNrCE1o2ZOPztg');

$call->setVideoId('-9T5fLmn7rY');

$call->setPlaylistId('PLC1og_v3eb4hz6-dhmxJJfVsVRniUmKar');

//echo json_encode($call->getVideoSearch('tokyo', '', '',10, 'viewCount'));

//$call->getChannelVideosSearch('tokyo', '', '',10, 'viewCount');
//echo $call->getUserTotalVideoCount();


//echo json_encode($call->searchQuickLookArray());

echo json_encode($call->getVideoData());
//$call->getVideoData();

//echo $call->getVideoDuration(true);

//$call->getChannelData();
//echo json_encode($call->getChannelData());

//echo $call->getChannelStarted();


//echo json_encode($call->getChannelPlaylistsData());
//$call->getChannelPlaylistsData(50);

//echo json_encode($call->playlistsQuickLookArray());


//echo json_encode($call->getPlaylistsData());
//$call->getPlaylistsData(50);

//echo json_encode($call->playlistsQuickLookArray());




//echo $call->accessCodeUrl();
//echo json_encode($call->getUserDetails('shroud'));
//echo json_encode($call->getUserStream('johnmclane666'));
//echo json_encode($call->getTopStreams('',99));
//echo json_encode($call->getGameTopStreams(516575));
//echo json_encode($call->getGameData(516575));