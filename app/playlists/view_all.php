<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/header.php';

$user_id = $_SESSION['user_id'];

// Ενιαίο query: δικές σου ή δημόσιες όσων ακολουθείς
$stmt = $pdo->prepare("
  SELECT p.*, u.username 
  FROM playlists p
  JOIN users u ON u.id = p.user_id
  LEFT JOIN follows f ON f.followee_id = p.user_id AND f.follower_id = ?
  WHERE p.user_id = ? OR (f.follower_id IS NOT NULL AND p.is_public = 1)
");
$stmt->execute([$user_id, $user_id]);
$playlists = $stmt->fetchAll();
?>

<h2>Λίστες</h2>
<ul>
  <?php foreach ($playlists as $pl): ?>
    <li>
      <a href="view.php?id=<?= $pl['id'] ?>"><?= htmlspecialchars($pl['name']) ?></a>
      (του χρήστη <?= htmlspecialchars($pl['username']) ?>)

      <?php if ($pl['user_id'] == $_SESSION['user_id']): ?>
        | <a href="edit.php?id=<?= $pl['id'] ?>">✏️</a>
        | <a href="add_video.php?id=<?= $pl['id'] ?>">➕ Προσθήκη video</a>
        | 
        <form method="post" action="delete.php" style="display:inline;">
          <input type="hidden" name="playlist_id" value="<?= $pl['id'] ?>">
          <button type="submit" onclick="return confirm('Να διαγραφεί η λίστα;')">🗑️</button>
        </form>
      <?php endif; ?>
    </li>
  <?php endforeach; ?>
</ul>

<p><a href="create.php" class="btn">➕ Δημιουργία νέας λίστας</a></p>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
