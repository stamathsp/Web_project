<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/session.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $playlist_id = $_POST['playlist_id'] ?? null;
    $video_id = $_POST['video_id'] ?? null;
    $title = $_POST['title'] ?? null;
    $user_id = $_SESSION['user_id'];

    if (!$playlist_id || !$video_id || !$title) {
        die("Λείπουν δεδομένα.");
    }

    $stmt = $pdo->prepare("INSERT INTO videos (playlist_id, user_id, video_id, title) VALUES (?, ?, ?, ?)");
    $stmt->execute([$playlist_id, $user_id, $video_id, $title]);

    header("Location: ../playlists/view.php?id=$playlist_id");
    exit;
}
