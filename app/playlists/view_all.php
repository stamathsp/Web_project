<?php
require_once('../includes/session.php');
require_once('../includes/db.php');
require_once('../includes/header.php');

$user_id = $_SESSION['user_id'];

// Οι δικές σου λίστες
$stmt = $pdo->prepare("SELECT * FROM playlists WHERE user_id = ?");
$stmt->execute([$user_id]);
$own_playlists = $stmt->fetchAll();

// Οι δημόσιες λίστες όσων ακολουθείς
$stmt = $pdo->prepare("
  SELECT p.*, u.username FROM playlists p
  JOIN follows f ON p.user_id = f.followed_id
  JOIN users u ON u.id = p.user_id
  WHERE f.follower_id = ? AND p.is_public = 1
");
$stmt->execute([$user_id]);
$follow_playlists = $stmt->fetchAll();
?>

<h2>Οι Λίστες μου</h2>
<ul>
  <?php foreach ($own_playlists as $pl): ?>
    <li><a href="view.php?id=<?= $pl['id'] ?>"><?= htmlspecialchars($pl['title']) ?></a></li>
  <?php endforeach; ?>
</ul>

<h2>Λίστες αυτών που ακολουθείς</h2>
<ul>
  <?php foreach ($follow_playlists as $pl): ?>
    <li>
      <a href="view.php?id=<?= $pl['id'] ?>"><?= htmlspecialchars($pl['title']) ?></a>
      (του χρήστη <?= htmlspecialchars($pl['username']) ?>)
    </li>
  <?php endforeach; ?>
</ul>

<a href="create.php">+ Δημιουργία νέας λίστας</a>

<?php require_once('../includes/footer.php'); ?>
