<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/header.php';

$viewer_id = $_SESSION['user_id'];
$profile_id = $_GET['id'] ?? null;

// Αν δεν υπάρχει id ή είναι ο εαυτός μας, πήγαινε στο προσωπικό προφίλ
if (!$profile_id || $profile_id == $viewer_id) {
    header("Location: view.php");
    exit;
}

// Φέρνουμε τα στοιχεία του χρήστη
$stmt = $pdo->prepare("SELECT username FROM users WHERE id = ?");
$stmt->execute([$profile_id]);
$user = $stmt->fetch();

if (!$user) {
    echo "<p> Ο χρήστης δεν βρέθηκε.</p>";
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

// Ελέγχεις αν ο current user τον ακολουθεί
$stmt = $pdo->prepare("SELECT 1 FROM follows WHERE follower_id = ? AND followee_id = ?");
$stmt->execute([$viewer_id, $profile_id]);
$isFollowing = $stmt->fetch() ? true : false;

// Φέρνουμε τις δημόσιες λίστες του προφίλ
$stmt = $pdo->prepare("SELECT id, name FROM playlists WHERE user_id = ? AND is_public = 1");
$stmt->execute([$profile_id]);
$playlists = $stmt->fetchAll();


// Βρες το πλήθος τραγουδιών στη μεγαλύτερη λίστα του χρήστη B
$stmt = $pdo->prepare("
    SELECT MAX(song_count) FROM (
        SELECT COUNT(*) AS song_count
        FROM playlists p
        JOIN playlist_videos ps ON ps.playlist_id = p.id
        WHERE p.user_id = ? AND p.is_public = 1
        GROUP BY p.id
    ) AS b
");
$stmt->execute([$profile_id]);
$maxB = $stmt->fetchColumn() ?? 0;

// Βρες τη μεγαλύτερη λίστα (σε τραγούδια) από τους χρήστες που ακολουθεί ο A (viewer)
$stmt = $pdo->prepare("
    SELECT u.username, MAX(song_count) AS max_count FROM (
        SELECT p.user_id, COUNT(*) AS song_count
        FROM follows f
        JOIN playlists p ON p.user_id = f.followee_id
        JOIN playlist_videos ps ON ps.playlist_id = p.id
        WHERE f.follower_id = ? AND f.followee_id != ? AND p.is_public = 1
        GROUP BY p.id
    ) AS others
    JOIN users u ON others.user_id = u.id
    GROUP BY others.user_id
    ORDER BY max_count DESC
    LIMIT 1
");
$stmt->execute([$viewer_id, $profile_id]);
$row = $stmt->fetch();

if ($row && $row['max_count'] > $maxB) {
    echo "<p style='color: darkred; font-weight: bold;'>Ο χρήστης " . htmlspecialchars($row['username']) .
         " έχει τη μεγαλύτερη λίστα με " . (int)$row['max_count'] . " τραγούδια.</p>";
}

// Βρες το μέγιστο πλήθος δημόσιων λιστών που έχει κάποιος χρήστης
$stmt = $pdo->query("SELECT MAX(cnt) AS max_playlists FROM (SELECT COUNT(*) AS cnt FROM playlists WHERE is_public = 1 GROUP BY user_id) AS sub");
$maxPlaylists = $stmt->fetchColumn();

// Πόσες δημόσιες λίστες έχει ο χρήστης του προφίλ;
$userPlaylistsCount = count($playlists);
?>

<h2>👤 Προφίλ χρήστη: <?= htmlspecialchars($user['username']) ?></h2>

<?php if ($userPlaylistsCount == $maxPlaylists && $maxPlaylists > 0): ?>
  <div style="color: green; font-weight: bold;">
     Αυτός ο χρήστης έχει τις περισσότερες δημόσιες λίστες (<?= $userPlaylistsCount ?>)!
  </div>
<?php endif; ?>

<!-- Κουμπί Follow/Unfollow -->
<form method="post" action="follow_action.php">
  <input type="hidden" name="target_id" value="<?= $profile_id ?>">
  <button type="submit" name="action" value="<?= $isFollowing ? 'unfollow' : 'follow' ?>">
    <?= $isFollowing ? ' Unfollow' : '➕ Follow' ?>
  </button>
</form>

<!-- Λίστες -->
<h3>📂 Δημόσιες λίστες</h3>
<?php if (empty($playlists)): ?>
  <p>Δεν υπάρχουν δημόσιες λίστες.</p>
<?php else: ?>
  <ul>
    <?php foreach ($playlists as $pl): ?>
      <li>
        <a href="../playlists/view.php?id=<?= $pl['id'] ?>">
          <?= htmlspecialchars($pl['name']) ?>
        </a>
      </li>
    <?php endforeach; ?>
  </ul>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>