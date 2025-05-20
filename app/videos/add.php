<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/session.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $playlist_id = $_POST['playlist_id'] ?? null;
    $youtube_id = $_POST['video_id'] ?? null;  // ⚠️ Εδώ το όνομα πρέπει να ταιριάζει με το form
    $title = $_POST['title'] ?? null;
    $user_id = $_SESSION['user_id'];

    // Απλός έλεγχος για κενά δεδομένα
    if (!$playlist_id || !$youtube_id || !$title) {
        die("Λείπουν απαιτούμενα στοιχεία.");
    }

    $stmt = $pdo->prepare("INSERT INTO videos (playlist_id, youtube_id, title, added_by) VALUES (?, ?, ?, ?)");
    $stmt->execute([$playlist_id, $youtube_id, $title, $user_id]);

    // Προαιρετικά redirect πίσω στη λίστα
    header("Location: ../playlists/view.php?id=" . $playlist_id);
    exit;
}
?>
