<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/header.php';

$user_id = $_SESSION['user_id'];

// Οι δικές σου λίστες
$stmt = $pdo->prepare("SELECT * FROM playlists WHERE user_id = ?");
$stmt->execute([$user_id]);
$own_playlists = $stmt->fetchAll();

// Οι δημόσιες λίστες όσων ακολουθείς
$stmt = $pdo->prepare("
  SELECT p.*, u.username 
  FROM playlists p
  JOIN follows f ON f.followee_id = p.user_id
  JOIN users u ON u.id = p.user_id
  WHERE f.follower_id = ? AND p.is_public = 1
");
$stmt->execute([$user_id]);
$follow_playlists = $stmt->fetchAll();
?>

<h2>Οι Λίστες μου</h2>
<ul>
  <?php foreach ($own_playlists as $pl): ?>
    <li><a href="view.php?id=<?= $pl['id'] ?>"><?= htmlspecialchars($pl['name']) ?></a></li>
  <?php endforeach; ?>
</ul>

<h2>Λίστες αυτών που ακολουθείς</h2>
<ul>
  <?php foreach ($follow_playlists as $pl): ?>
    <li>
      <a href="view.php?id=<?= $pl['id'] ?>"><?= htmlspecialchars($pl['name']) ?></a>
      (του χρήστη <?= htmlspecialchars($pl['username']) ?>)
    </li>
  <?php endforeach; ?>
</ul>

<p><a href="create.php" class="btn">➕ Δημιουργία νέας λίστας</a></p>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
