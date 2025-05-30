<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/header.php';

// Έλεγχος αν ο χρήστης είναι αυθεντικοποιημένος
if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
    header('Location: /auth/login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Φόρμα Αναζήτησης - Τιμές από GET ή POST
$search = $_GET['search'] ?? '';
$from_date = $_GET['from_date'] ?? '';    
$to_date = $_GET['to_date'] ?? '';
$user_filter = $_GET['user_filter'] ?? '';

// Query με φίλτρα
$query = "
SELECT DISTINCT p.*, u.username, u.name AS firstname, u.surname AS lastname, u.email
FROM playlists p
JOIN users u ON p.user_id = u.id
LEFT JOIN videos v ON v.playlist_id = p.id
WHERE (p.user_id = :uid OR (p.is_public = 1 AND p.user_id IN (
    SELECT followee_id FROM follows WHERE follower_id = :uid
)))
";

$params = ['uid' => $user_id];

if ($search) {
    $query .= " AND (
        p.name LIKE :search OR
        v.title LIKE :search
    )";
    $params['search'] = "%$search%";
}

if ($from_date && $to_date) {
    $query .= " AND DATE(p.created_at) BETWEEN :from AND :to";
    $params['from'] = $from_date;
    $params['to'] = $to_date;
}

if ($user_filter) {
    $query .= " AND (
        u.username LIKE :uf OR
        u.name LIKE :uf OR
        u.surname LIKE :uf OR
        u.email LIKE :uf
    )";
    $params['uf'] = "%$user_filter%";
}

$query .= " ORDER BY p.created_at DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$playlists = $stmt->fetchAll();
?>

<h2>🔍 Αναζήτηση Λιστών</h2>
<form method="GET" style="margin-bottom: 20px;">
  <input type="text" name="search" placeholder="Τίτλος ή περιεχόμενο" value="<?= htmlspecialchars($search) ?>">
  <input type="date" name="from_date" value="<?= htmlspecialchars($from_date) ?>">
  <input type="date" name="to_date" value="<?= htmlspecialchars($to_date) ?>">
  <input type="text" name="user_filter" placeholder="Όνομα, επώνυμο, username ή email" value="<?= htmlspecialchars($user_filter) ?>">
  <button type="submit">Αναζήτηση</button>
</form>

<h2>Λίστες</h2>
<ul>
  <?php if ($playlists): ?>
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
  <?php else: ?>
    <li>⚠️ Δεν βρέθηκαν λίστες.</li>
  <?php endif; ?>



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
