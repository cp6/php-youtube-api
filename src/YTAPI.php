<?php

namespace Corbpie\YouTubeApiClass;

use Corbpie\YouTubeApiClass\ytApiException as yte;

class YTAPI
{
    private const API_KEY = 'YOUTUBE-API-KEY-HERE';//Your YouTube API key
    private const URI = 'https://www.googleapis.com/youtube/v3';//Do NOT change
    private string $channel_id;
    private string $video_id;
    private string $playlist_id;
    private array $search_data;
    private array $video_data;
    private array $channel_data;
    private array $channel_playlist_data;
    private array $playlist_data;

    public function setChannelId(string $channel_id): void
    {
        $this->channel_id = $channel_id;
    }

    public function setVideoId(string $video_id): void
    {
        $this->video_id = $video_id;
    }

    public function setPlaylistId(string $playlist_id): void
    {
        $this->playlist_id = $playlist_id;
    }

    private function doCurl(string $url): array
    {
        $crl = curl_init(self::URI . $url);
        curl_setopt($crl, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($crl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($crl, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($crl, CURLOPT_TIMEOUT, 20);
        curl_setopt($crl, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($crl, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($crl, CURLOPT_ENCODING, 'gzip,deflate');
        curl_setopt($crl, CURLOPT_RETURNTRANSFER, true);
        $call_response = curl_exec($crl);
        $responseInfo = curl_getinfo($crl);
        curl_close($crl);
        if ($responseInfo['http_code'] === 200) {
            return json_decode($call_response, true);
        }
        return array('http_response_code' => $responseInfo['http_code']);//Call failed
    }

    public function getVideoSearch(string $query = '', string $date_before = '', string $date_after = '', int $results = 50, string $order = 'viewCount', string $page_token = ''): array
    {
        ($this->channel_id !== '' || is_null($this->channel_id)) ? $ch = "channelId=" . $this->channel_id : $ch = "";
        ($page_token !== '') ? $pt = "&pageToken=" . $page_token : $pt = "";
        ($query !== '') ? $q = "&q=" . $query : $q = "";
        ($date_before !== '') ? $dt_b = "&publishedBefore=" . $date_before : $dt_b = "";
        ($date_after !== '') ? $dt_a = "&publishedBefore=" . $date_after : $dt_a = "";
        return $this->search_data = $this->doCurl("/search?part=snippet{$ch}&maxResults=$results{$dt_a}{$dt_b}&order=$order{$q}&type=video$pt&key=" . self::API_KEY);
    }

    public function searchQuickLookArray(): array
    {
        $this->verifySearchDataSet();
        $arr = array();
        foreach ($this->search_data['items'] as $v) {
            $arr[] = array(
                'video_id' => $v['id']['videoId'],
                'title' => $v['snippet']['title'],
                'published' => $this->youtubeDateFormat($v['snippet']['publishedAt'])
            );
        }
        return $arr;
    }

    public function getUserTotalVideoCount(): ?int
    {
        $this->verifySearchDataSet();
        return $this->search_data['pageInfo']['totalResults'] ?? null;
    }

    public function getSearchVideoId(int $index = 0): ?string
    {
        $this->verifySearchDataSet();
        return $this->search_data['items'][$index]['id']['videoId'] ?? null;
    }

    public function getSearchVideoTitle(int $index = 0): ?string
    {
        $this->verifySearchDataSet();
        return $this->search_data['items'][$index]['snippet']['title'] ?? null;
    }

    public function getSearchVideoDate(int $index = 0, bool $format = true): ?string
    {
        $this->verifySearchDataSet();
        if (isset($this->search_data['items'][$index]['snippet']['publishedAt'])) {
            if (!$format) {
                return $this->search_data['items'][$index]['snippet']['publishedAt'];
            }
            return $this->youtubeDateFormat($this->search_data['items'][$index]['snippet']['publishedAt']);
        }
        return null;
    }

    public function getSearchVideoDesc(int $index = 0): ?string
    {
        $this->verifySearchDataSet();
        return $this->search_data['items'][$index]['snippet']['description'] ?? null;
    }

    public function getSearchVideoThumbnail(int $index = 0): ?string
    {
        $this->verifySearchDataSet();
        return $this->search_data['items'][$index]['snippet']['thumbnails']['high']['url'] ?? null;
    }

    public function getVideoData(): array
    {
        $this->verifyVideoIdSet();
        return $this->video_data = $this->doCurl("/videos?part=snippet,statistics,contentDetails&id={$this->video_id}&key=" . self::API_KEY);
    }

    public function getVideoViews(): ?int
    {
        $this->verifyVideoDataSet();
        return $this->video_data['items'][0]['statistics']['viewCount'] ?? null;
    }

    public function getVideoLikes(): ?int
    {
        $this->verifyVideoDataSet();
        return $this->video_data['items'][0]['statistics']['likeCount'] ?? null;
    }

    public function getVideoDisLikes(): ?int
    {
        $this->verifyVideoDataSet();
        return $this->video_data['items'][0]['statistics']['dislikeCount'] ?? null;
    }

    public function getVideoCommentCount(): ?int
    {
        $this->verifyVideoDataSet();
        return $this->video_data['items'][0]['statistics']['commentCount'] ?? null;
    }

    public function getVideoTitle(): ?string
    {
        $this->verifyVideoDataSet();
        return $this->video_data['items'][0]['snippet']['title'] ?? null;
    }

    public function getVideoDesc(): ?string
    {
        $this->verifyVideoDataSet();
        return $this->video_data['items'][0]['snippet']['description'] ?? null;
    }

    public function getVideoThumbnail(): ?string
    {
        $this->verifyVideoDataSet();
        return $this->video_data['items'][0]['snippet']['thumbnails']['maxres']['url'] ?? $this->video_data['items'][0]['snippet']['thumbnails']['standard']['url'] ?? null;
    }

    public function getVideoTags(): array
    {
        $this->verifyVideoDataSet();
        return $this->video_data['items'][0]['snippet']['tags'] ?? [];
    }

    public function getVideoCategoryId(): ?int
    {
        $this->verifyVideoDataSet();
        return $this->video_data['items'][0]['snippet']['categoryId'] ?? null;
    }

    public function getVideoDefaultLanguage(): ?string
    {
        $this->verifyVideoDataSet();
        return $this->video_data['items'][0]['snippet']['defaultLanguage'] ?? null;
    }

    public function getVideoDuration(bool $format = false): ?string
    {
        $this->verifyVideoDataSet();
        if (isset($this->video_data['items'][0]['contentDetails']['duration'])) {
            $duration = $this->video_data['items'][0]['contentDetails']['duration'];
            if (!$format) {
                return $duration;
            }
            $di = new \DateInterval($duration);
            return sprintf("%02s:%02s:%02s", $di->h, $di->i, $di->s);
        }
        return null;
    }

    public function getVideoDefinition(): ?string
    {
        $this->verifyVideoDataSet();
        return $this->video_data['items'][0]['contentDetails']['definition'] ?? null;
    }

    public function getChannelData(): array
    {
        $this->verifyChannelIdSet();
        return $this->channel_data = $this->doCurl("/channels?part=snippet,statistics,brandingSettings&id={$this->channel_id}&key=" . self::API_KEY);
    }

    public function getChannelTitle(): ?string
    {
        $this->verifyChannelDataSet();
        return $this->channel_data['items'][0]['snippet']['title'] ?? null;
    }

    public function getChannelDesc(): ?string
    {
        $this->verifyChannelDataSet();
        return $this->channel_data['items'][0]['snippet']['description'] ?? null;
    }

    public function getChannelCustomURL(): ?string
    {
        $this->verifyChannelDataSet();
        return $this->channel_data['items'][0]['snippet']['customUrl'] ?? null;
    }

    public function getChannelStarted(bool $format = true): ?string
    {
        $this->verifyChannelDataSet();
        if (isset($this->channel_data['items'][0]['snippet']['publishedAt'])) {
            if (!$format) {
                return $this->channel_data['items'][0]['snippet']['publishedAt'];
            }
            return $this->youtubeDateFormat($this->channel_data['items'][0]['snippet']['publishedAt']);
        }
        return null;
    }

    public function getChannelCountry(): ?string
    {
        $this->verifyChannelDataSet();
        return $this->channel_data['items'][0]['snippet']['country'] ?? null;
    }

    public function getChannelBannerLarge(): ?string
    {
        $this->verifyChannelDataSet();
        return $this->channel_data['items'][0]['brandingSettings']['image']['bannerImageUrl'] ?? null;
    }

    public function getChannelAvatarLarge(): ?string
    {
        $this->verifyChannelDataSet();
        return $this->channel_data['items'][0]['snippet']['thumbnails']['high']['url'] ?? null;
    }

    public function getChannelAvatar(): ?string
    {
        $this->verifyChannelDataSet();
        return $this->channel_data['items'][0]['snippet']['thumbnails']['medium']['url'] ?? null;
    }

    public function getChannelTotalViews(): ?int
    {
        $this->verifyChannelDataSet();
        return $this->channel_data['items'][0]['statistics']['viewCount'] ?? null;
    }

    public function getChannelTotalSubs(): ?int
    {
        $this->verifyChannelDataSet();
        return $this->channel_data['items'][0]['statistics']['subscriberCount'] ?? null;
    }

    public function getChannelTotalVideos(): ?int
    {
        $this->verifyChannelDataSet();
        return $this->channel_data['items'][0]['statistics']['videoCount'] ?? null;
    }

    public function getChannelHiddenSubCount(): bool
    {
        $this->verifyChannelDataSet();
        return $this->channel_data['items'][0]['statistics']['hiddenSubscriberCount'];
    }

    public function getChannelPlaylistsData(int $results = 50): array
    {
        $this->verifyChannelPlaylistsDataSet();
        return $this->channel_playlist_data = $this->doCurl("/playlists?part=snippet,id,contentDetails&channelId={$this->channel_id}&maxResults=$results&key=" . self::API_KEY);
    }

    public function getChannelPlaylistsAmount(): ?int
    {
        $this->verifyChannelPlaylistsDataSet();
        return $this->channel_playlist_data['pageInfo']['totalResults'] ?? null;
    }

    public function channelPlaylistsQuickLookArray(): array
    {
        $this->verifyChannelPlaylistsDataSet();
        $arr = array();
        foreach ($this->channel_playlist_data['items'] as $pl) {
            $arr[] = array(
                'playlist_id' => $pl['id'],
                'title' => $pl['snippet']['title'],
                'published' => $this->youtubeDateFormat($pl['snippet']['publishedAt']),
                'videos' => $pl['contentDetails']['itemCount']
            );
        }
        return $arr;
    }

    public function getPlaylistsData(string $page_token = '', int $results = 50): array
    {
        ($page_token !== '') ? $pt = "&pageToken=" . $page_token : $pt = "";
        $this->verifyPlaylistIdSet();
        return $this->playlist_data = $this->doCurl("/playlistItems?part=snippet,id,contentDetails&playlistId={$this->playlist_id}&maxResults=$results{$pt}&key=" . self::API_KEY);
    }

    public function playlistsQuickLookArray(): array
    {
        $this->verifyPlaylistDataSet();
        $arr = array();
        foreach ($this->playlist_data['items'] as $pl) {
            $arr[] = array(
                'video_id' => $pl['contentDetails']['videoId'],
                'title' => $pl['snippet']['title'],
                'position' => $pl['snippet']['position'],
                'published' => $this->youtubeDateFormat($pl['snippet']['publishedAt']),
            );
        }
        return $arr;
    }

    private function youtubeDateFormat(string $date, string $format_as = 'Y-m-d H:i:s'): string
    {
        return date($format_as, strtotime(str_replace(["T0", "Z"], [" ", ""], $date)));
    }

    private function verifyVideoDataSet(): void
    {
        try {
            if (!isset($this->video_data)) {
                throw new ytApiException("Array video_data has not been set. Please use getVideoData() first");
            }
        } catch (ytApiException $e) {//display error message
            echo $e->errorMessage();
        }
    }

    private function verifyChannelDataSet(): void
    {
        try {
            if (!isset($this->channel_data)) {
                throw new ytApiException("Array channel_data has not been set. Please use getChannelData() first");
            }
        } catch (ytApiException $e) {//display error message
            echo $e->errorMessage();
        }
    }

    private function verifyChannelPlaylistsDataSet(): void
    {
        try {
            if (!isset($this->channel_playlist_data)) {
                throw new ytApiException("Array channel_playlist_data has not been set. Please use getChannelPlaylistsData() first");
            }
        } catch (ytApiException $e) {//display error message
            echo $e->errorMessage();
        }
    }

    private function verifyPlaylistDataSet(): void
    {
        try {
            if (!isset($this->playlist_data)) {
                throw new ytApiException("Array playlist_data has not been set. Please use getPlaylistsData() first");
            }
        } catch (ytApiException $e) {//display error message
            echo $e->errorMessage();
        }
    }

    private function verifySearchDataSet(): void
    {
        try {
            if (!isset($this->search_data)) {
                throw new ytApiException("Array search_data has not been set. Please use getVideoSearch() first");
            }
        } catch (ytApiException $e) {//display error message
            echo $e->errorMessage();
        }
    }

    private function verifyChannelIdSet(): void
    {
        try {
            if (!isset($this->channel_id)) {
                throw new ytApiException("channel_id has not been set. Please use setChannelId() first");
            }
        } catch (ytApiException $e) {//display error message
            echo $e->errorMessage();
        }
    }

    private function verifyVideoIdSet(): void
    {
        try {
            if (!isset($this->video_id)) {
                throw new ytApiException("video_id has not been set. Please use setVideoId() first");
            }
        } catch (ytApiException $e) {//display error message
            echo $e->errorMessage();
        }
    }

    private function verifyPlaylistIdSet(): void
    {
        try {
            if (!isset($this->playlist_id)) {
                throw new ytApiException("playlist_id has not been set. Please use setPlaylistId() first");
            }
        } catch (ytApiException $e) {//display error message
            echo $e->errorMessage();
        }
    }
}