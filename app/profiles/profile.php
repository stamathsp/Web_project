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
    echo "<p>❌ Ο χρήστης δεν βρέθηκε.</p>";
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
?>

<h2>👤 Προφίλ χρήστη: <?= htmlspecialchars($user['username']) ?></h2>

<!-- Κουμπί Follow/Unfollow -->
<form method="post" action="follow_action.php">
  <input type="hidden" name="target_id" value="<?= $profile_id ?>">
  <button type="submit" name="action" value="<?= $isFollowing ? 'unfollow' : 'follow' ?>">
    <?= $isFollowing ? '❌ Unfollow' : '➕ Follow' ?>
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
