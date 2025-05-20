<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/session.php';


$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Διαγραφή videos
    $stmt = $pdo->prepare("DELETE FROM videos WHERE playlist_id IN (SELECT id FROM playlists WHERE user_id = ?)");
    $stmt->execute([$user_id]);

    // Διαγραφή playlists
    $stmt = $pdo->prepare("DELETE FROM playlists WHERE user_id = ?");
    $stmt->execute([$user_id]);

    // Διαγραφή follows (αν υπάρχει πίνακας follows)
    $stmt = $pdo->prepare("DELETE FROM follows WHERE follower_id = ? OR followee_id = ?");
    $stmt->execute([$user_id, $user_id]);

    // Διαγραφή χρήστη
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$user_id]);

    // Καθαρισμός session
    session_unset();
    session_destroy();

    header("Location: ../index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <title>Διαγραφή Προφίλ</title>
    <link rel="stylesheet" href="/public/css/styles.css">
</head>
<body>
<?php include '../includes/header.php'; ?>
<main>
    <h2>Διαγραφή Λογαριασμού</h2>
    <p><strong>Προσοχή:</strong> Αυτή η ενέργεια είναι <u>μη αναστρέψιμη</u> και θα διαγραφούν όλα τα δεδομένα σου (λίστες, περιεχόμενα, ακολουθίες).</p>
    <form method="POST">
        <button type="submit" onclick="return confirm('Είσαι απόλυτα σίγουρος; Αυτή η ενέργεια είναι μη αναστρέψιμη!')">
            ✅ Ναι, διαγραφή λογαριασμού
        </button>
        <a href="view.php">❌ Άκυρο, γύρνα πίσω</a>
    </form>
</main>
<?php include '../includes/footer.php'; ?>
</body>
</html>
