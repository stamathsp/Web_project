<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/session.php';

$playlist_id = $_GET['id'] ?? null;
if (!$playlist_id) {
    echo "<p>Η λίστα δεν βρέθηκε.</p>";
    exit;
}

// Έλεγχος ιδιοκτησίας
$stmt = $pdo->prepare("SELECT * FROM playlists WHERE id = ? AND user_id = ?");
$stmt->execute([$playlist_id, $_SESSION['user_id']]);
$playlist = $stmt->fetch();

if (!$playlist) {
    echo "<p>Δεν έχεις πρόσβαση σε αυτή τη λίστα.</p>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $is_public = isset($_POST['is_public']) ? 1 : 0;

    if ($name === '') {
        die('Το όνομα δεν μπορεί να είναι κενό.');
    }

    $stmt = $pdo->prepare("UPDATE playlists SET name = ?, is_public = ? WHERE id = ?");
    $stmt->execute([$name, $is_public, $playlist_id]);

    header("Location: view.php?id=$playlist_id");
    exit;
}

// 🟢 Αφού ΔΕΝ έγινε redirect, τώρα φορτώνουμε το UI:
require_once __DIR__ . '/../includes/header.php';
?>

<h2>Επεξεργασία Λίστας</h2>

<form method="post">
  <label>Τίτλος:<br>
    <input type="text" name="name" value="<?= htmlspecialchars($playlist['name'] ?? '') ?>" required>
  </label><br><br>

  <label>
    <input type="checkbox" name="is_public" <?= $playlist['is_public'] ? 'checked' : '' ?>>
    Δημόσια λίστα
  </label><br><br>

  <button type="submit">Αποθήκευση</button>
</form>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
