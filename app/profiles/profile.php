<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/header.php';

$viewer_id = $_SESSION['user_id'];
$profile_id = $_GET['id'] ?? null;

if (!$profile_id || $profile_id == $viewer_id) {
    header("Location: view.php"); // πήγαινε στο δικό σου προφίλ
    exit;
}

// Πληροφορίες χρήστη
$stmt = $pdo->prepare("SELECT username FROM users WHERE id = ?");
$stmt->execute([$profile_id]);
$user = $stmt->fetch();

if (!$user) {
    echo "<p>Ο χρήστης δεν βρέθηκε.</p>";
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

// Έλεγχος αν τον ακολουθώ
$stmt = $pdo->prepare("SELECT * FROM follows WHERE follower_id = ? AND followee_id = ?");
$stmt->execute([$viewer_id, $profile_id]);
$isFollowing = $stmt->fetch() ? true : false;

// Δημόσιες λίστες
$stmt = $pdo->prepare("SELECT id, name FROM playlists WHERE user_id = ? AND is_public = 1");
$stmt->execute([$profile_id]);
$playlists = $stmt->fetchAll();
?>

<h2>👤 Προφίλ χρήστη: <?= htmlspecialchars($user['username']) ?></h2>

<form method="post" action="follow_action.php">
  <input type="hidden" name="target_id" value="<?= $profile_id ?>">
  <button type="submit" name="action" value="<?= $isFollowing ? 'unfollow' : 'follow' ?>">
    <?= $isFollowing ? '❌ Unfollow' : '➕ Follow' ?>
  </button>
</form>

<h3>📂 Δημόσιες λίστες</h3>
<ul>
  <?php foreach ($playlists as $pl): ?>
    <li>
      <a href="../playlists/view.php?id=<?= $pl['id'] ?>">
        <?= htmlspecialchars($pl['name']) ?>
      </a>
    </li>
  <?php endforeach; ?>
</ul>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
