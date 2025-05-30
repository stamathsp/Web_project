<?php
require_once __DIR__ . '/secrets.php';

function searchYouTube($query, $pageToken = null, $maxResults = 5) {
    $apiKey = YOUTUBE_API_KEY;
    $url = "https://www.googleapis.com/youtube/v3/search?part=snippet&type=video&maxResults=$maxResults&q=" . urlencode($query) . "&type=video&key=$apiKey";

    if ($pageToken) {
        $url .= "&pageToken=" . urlencode($pageToken);
    }

    $response = @file_get_contents($url);
    if ($response === false) return [];

    return json_decode($response, true);
}
