<?php

require_once realpath(__DIR__ . '/../vendor/autoload.php');
require_once __DIR__ . '/../includes/db.php';

use Symfony\Component\Yaml\Yaml;

header('Content-Type: text/yaml');

$data = [];

$query = "
    SELECT 
        p.id AS playlist_id,
        p.name AS playlist_title,
        v.title AS video_title,
        v.youtube_id,   
        v.added_at,
        u.username,
        u.password
    FROM playlists p
    JOIN videos v ON v.playlist_id = p.id
    JOIN users u ON u.id = v.added_by
    WHERE p.is_public = 1
    ORDER BY p.id, v.added_at
";

$stmt = $pdo->prepare($query);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($results as $row) {
    $uid_hash = hash('sha256', $row['username'] . $row['password']);
    $pid = $row['playlist_id'];

    if (!isset($data[$pid])) {
        $data[$pid] = [
            'playlist_id'   => $pid,
            'playlist_name' => $row['playlist_title'],
            'owner_hash'    => $uid_hash,
            'videos'        => []
        ];
    }

    $data[$pid]['videos'][] = [
        'title'       => $row['video_title'],
        'youtube_id'  => $row['youtube_id'],
        'added_at'    => $row['added_at']
    ];
}

echo Yaml::dump(array_values($data), 2, 4);
