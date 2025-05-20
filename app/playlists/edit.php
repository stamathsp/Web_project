<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/header.php';


$playlist_id = $_GET['id'] ?? null;
if (!$playlist_id) {
    echo "<p>Η λίστα δεν βρέθηκε.</p>";
    require_once('../includes/footer.php');
    exit;
}

// Έλεγχος ιδιοκτησίας
$stmt = $pdo->prepare("SELECT * FROM playlists WHERE id = ? AND user_id = ?");
$stmt->execute([$playlist_id, $_SESSION['user_id']]);
$playlist = $stmt->fetch();

if (!$playlist) {
    echo "<p>Δεν έχεις πρόσβαση σε αυτή τη λίστα.</p>";
    require_once('../includes/footer.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $is_public = isset($_POST['is_public']) ? 1 : 0;

    $stmt = $pdo->prepare("UPDATE playlists SET title = ?, is_public = ? WHERE id = ?");
    $stmt->execute([$title, $is_public, $playlist_id]);

    header("Location: view.php?id=$playlist_id");
    exit;
}
?>

<h2>Επεξεργασία Λίστας</h2>

<form method="post">
  <label>Τίτλος:<br><input type="text" name="title" value="<?= htmlspecialchars($playlist['title']) ?>" required></label><br><br>

  <label>
    <input type="checkbox" name="is_public" <?= $playlist['is_public'] ? 'checked' : '' ?>>
    Δημόσια λίστα
  </label><br><br>

  <button type="submit">Αποθήκευση</button>
</form>

<?php require_once('../includes/footer.php'); ?>
