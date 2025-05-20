<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/session.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['playlist_id'])) {
    $playlist_id = $_POST['playlist_id'];
    $user_id = $_SESSION['user_id'];

    // Διαγραφή μόνο από τον πίνακα playlists
    $stmt = $pdo->prepare("DELETE FROM playlists WHERE id = ? AND user_id = ?");
    $stmt->execute([$playlist_id, $user_id]);

    header('Location: view_all.php');
    exit;
}
