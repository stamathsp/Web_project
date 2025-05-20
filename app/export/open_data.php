<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../includes/db.php';

use Symfony\Component\Yaml\Yaml;

header('Content-Type: text/yaml');

$data = [];

$query = "
SELECT p.id AS playlist_id, p.title AS playlist_title, p.is_public,
       v.title AS video_title, v.url, v.created_at,
       u.username, u.password
FROM playlists p
JOIN videos v ON v.playlist_id = p.id
JOIN users u ON u.id = v.user_id
WHERE p.is_public = 1
ORDER BY p.id, v.created_at";

$stmt = $pdo->prepare($query);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($results as $row) {
    $uid_hash = hash('sha256', $row['username'] . $row['password']);
    $pid = $row['playlist_id'];

    if (!isset($data[$pid])) {
        $data[$pid] = [
            'playlist_title' => $row['playlist_title'],
            'user_hash' => $uid_hash,
            'videos' => []
        ];
    }

    $data[$pid]['videos'][] = [
        'title' => $row['video_title'],
        'url' => $row['url'],
        'created_at' => $row['created_at']
    ];
}

echo Yaml::dump(array_values($data), 4, 2);
