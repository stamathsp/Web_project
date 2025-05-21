<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/header.php';

$me = $_SESSION['user_id'];
$search = $_GET['q'] ?? '';
$like = '%' . $search . '%';

// Φέρνουμε όλους τους χρήστες εκτός από τον εαυτό μας, με φίλτρο ονόματος
$stmt = $pdo->prepare("
  SELECT u.id, u.username,
         (SELECT 1 FROM follows f WHERE f.follower_id = ? AND f.followee_id = u.id) AS is_following
  FROM users u
  WHERE u.id != ? AND u.username LIKE ?
  ORDER BY u.username ASC
");
$stmt->execute([$me, $me, $like]);
$users = $stmt->fetchAll();
?>

<h2>🔍 Ανακάλυψε χρήστες</h2>

<!-- Φόρμα Αναζήτησης -->
<form method="get" action="" style="margin-bottom: 1rem;">
  <input type="text" name="q" value="<?= htmlspecialchars($search) ?>" placeholder="Αναζήτηση ονόματος">
  <button type="submit">🔍</button>
</form>

<!-- Αποτελέσματα -->
<?php if (empty($users)): ?>
  <p>Δεν βρέθηκαν χρήστες.</p>
<?php else: ?>
  <ul>
    <?php foreach ($users as $user): ?>
      <li>
        <a href="profile.php?id=<?= $user['id'] ?>">
          <?= htmlspecialchars($user['username']) ?>
        </a>

        <form method="post" action="follow_action.php" style="display:inline;">
          <input type="hidden" name="target_id" value="<?= $user['id'] ?>">
          <button type="submit" name="action" value="<?= $user['is_following'] ? 'unfollow' : 'follow' ?>">
            <?= $user['is_following'] ? '❌ Unfollow' : '➕ Follow' ?>
          </button>
        </form>
      </li>
    <?php endforeach; ?>
  </ul>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
