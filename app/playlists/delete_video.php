<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/session.php';

$video_id = $_POST['video_id'] ?? null;
$playlist_id = $_POST['playlist_id'] ?? null;
$user_id = $_SESSION['user_id'];

if (!$video_id || !$playlist_id) {
    die("❌ Λείπουν δεδομένα.");
}

// Επιβεβαίωση ότι η λίστα ανήκει στον χρήστη
$stmt = $pdo->prepare("SELECT * FROM playlists WHERE id = ? AND user_id = ?");
$stmt->execute([$playlist_id, $user_id]);
$playlist = $stmt->fetch();

if (!$playlist) {
    die("🚫 Δεν έχεις δικαίωμα να τροποποιήσεις αυτή τη λίστα.");
}

// Διαγραφή του video από τη λίστα (από τον πίνακα videos)
$stmt = $pdo->prepare("DELETE FROM videos WHERE id = ? AND playlist_id = ?");
$stmt->execute([$video_id, $playlist_id]);

header("Location: view.php?id=$playlist_id");
exit;
