<?php
// Αντικατάστησε το παρακάτω με το δικό σου API Key
define('YOUTUBE_API_KEY', 'AIzaSyAlhxwm-lHpm-Yi-v89YNBS3ZervQbsCjc');

// Συνάρτηση για αναζήτηση βίντεο
function searchYouTube($query, $maxResults = 10) {
    $apiKey = YOUTUBE_API_KEY;
    $url = "https://www.googleapis.com/youtube/v3/search?part=snippet&type=video&maxResults=$maxResults&q=" . urlencode($query) . "&key=$apiKey";

    $response = file_get_contents($url);
    if ($response === false) {
        return [];
    }

    $data = json_decode($response, true);
    return $data['items'] ?? [];
}
