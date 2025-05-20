<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/session.php';

$me = $_SESSION['user_id'];
$target = $_POST['target_id'] ?? null;
$action = $_POST['action'] ?? null;

if ($target && $me && $me != $target) {
    if ($action === 'follow') {
        $stmt = $pdo->prepare("INSERT IGNORE INTO follows (follower_id, followee_id) VALUES (?, ?)");
        $stmt->execute([$me, $target]);
    } elseif ($action === 'unfollow') {
        $stmt = $pdo->prepare("DELETE FROM follows WHERE follower_id = ? AND followee_id = ?");
        $stmt->execute([$me, $target]);
    }
}

header("Location: profile.php?id=$target");
exit;
