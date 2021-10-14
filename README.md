# PHP YouTube API wrapper

Just a PHP YouTube API wrapper for searching videos and getting channel/video/playlist data and information.

## Usage

```shell
composer require corbpie/yt-api
```

Put your YouTube API key at line 9 of ```src/YTAPI.php```

Use the class with:

```php
require_once('vendor/autoload.php');

use Corbpie\YouTubeApiClass\YTAPI;

$yt = new YTAPI();
```

## Searching videos

**Search a channel**

This will gets 25 videos from the NBA channel with the query "Jordan" sorted by view count

```php
$yt->setChannelId('UCWJ2lWNubArHWmf3FIHbfcQ');
$yt->getVideoSearch('Jordan', 'viewCount', 25);
```

**Search everywhere**

Just dont set channelId to search all of YouTube (Not a channel specific)

```php
$yt->getVideoSearch('Jordan', 'viewCount', 25);
```

**Get latest videos from a channel**

Get 25 videos sorted by date published (recent -> oldest)

```php
$yt->setChannelId('UCWJ2lWNubArHWmf3FIHbfcQ');
$yt->getVideoSearch('', 'date', 25);
```

## Video information

```php
$yt->setVideoId('1fjhIWJSxfw');
$yt->getVideoData();
```

## Channel information

```php
$yt->setChannelId('UCWJ2lWNubArHWmf3FIHbfcQ');
$yt->getChannelData();
```

Get channel playlists (Requires channelId to have been set):

```php
$call->getChannelPlaylistsData(50);
```

## Playlist videos

```php
$yt->setPlaylistId('UCWJ2lWNubArHWmf3FIHbfcQ');
$yt->getPlaylistsData();
```
