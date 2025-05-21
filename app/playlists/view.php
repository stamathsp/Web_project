<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/header.php';

$playlist_id = $_GET['id'] ?? null;
if (!$playlist_id) {
    echo "<p>Η λίστα δεν βρέθηκε.</p>";
    require_once('../includes/footer.php');
    exit;
}

// Πληροφορίες λίστας
$stmt = $pdo->prepare("
    SELECT playlists.*, users.username 
    FROM playlists 
    JOIN users ON playlists.user_id = users.id 
    WHERE playlists.id = ?
");
$stmt->execute([$playlist_id]);
$playlist = $stmt->fetch();

if (!$playlist) {
    echo "<p>Η λίστα δεν υπάρχει.</p>";
    require_once('../includes/footer.php');
    exit;
}

// Βίντεο της λίστας (από τον πίνακα videos)
$stmt = $pdo->prepare("
    SELECT * FROM videos 
    WHERE playlist_id = ?
    ORDER BY added_at DESC
");
$stmt->execute([$playlist_id]);
$videos = $stmt->fetchAll();
?>

<h2>📂 Λίστα: <?= htmlspecialchars($playlist['name']) ?></h2>
<p>👤 Δημιουργήθηκε από: <?= htmlspecialchars($playlist['username']) ?></p>
<p>🔒 Ορατότητα: <?= $playlist['is_public'] ? 'Δημόσια' : 'Ιδιωτική' ?></p>

<?php if ($_SESSION['user_id'] == $playlist['user_id']): ?>
    <a href="edit.php?id=<?= $playlist_id ?>">✏️ Επεξεργασία</a>
<?php endif; ?>

<h3>🎥 Περιεχόμενο</h3>
<?php if ($videos): ?>
    <ul>
        <?php foreach ($videos as $video): ?>
            <li style="margin-bottom: 2rem;">
                <p><strong><?= htmlspecialchars($video['title']) ?></strong></p>
                <iframe width="320" height="180"
                        src="https://www.youtube.com/embed/<?= htmlspecialchars($video['youtube_id']) ?>"
                        frameborder="0" allowfullscreen>
                </iframe>

                <?php if ($_SESSION['user_id'] == $playlist['user_id']): ?>
                    <form method="post" action="delete_video.php" style="display:inline;">
                        <input type="hidden" name="video_id" value="<?= $video['id'] ?>">
                        <input type="hidden" name="playlist_id" value="<?= $playlist_id ?>">
                        <button type="submit" onclick="return confirm('Να διαγραφεί αυτό το βίντεο;')">🗑️</button>
                    </form>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
<?php else: ?>
    <p>❗ Η λίστα δεν περιέχει ακόμα βίντεο.</p>
<?php endif; ?>

<?php if ($_SESSION['user_id'] == $playlist['user_id']): ?>
    <a href="add_video.php?id=<?= $playlist_id ?>" class="btn">➕ Προσθήκη Video</a>
<?php endif; ?>

<?php require_once('../includes/footer.php'); ?>
