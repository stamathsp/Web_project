<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/session.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $is_public = isset($_POST['is_public']) ? 1 : 0;
    $user_id = $_SESSION['user_id'];

    if ($title === '') {
        $error = "Το όνομα της λίστας δεν μπορεί να είναι κενό.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO playlists (user_id, name, is_public) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $title, $is_public]);

        $playlist_id = $pdo->lastInsertId();
        header("Location: view.php?id=$playlist_id");
        exit;
    }
}
?>

<?php require_once __DIR__ . '/../includes/header.php'; ?>

<h2>Δημιουργία Νέας Λίστας</h2>

<?php if (!empty($error)): ?>
  <p style="color:red;"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form method="post">
  <label>Τίτλος λίστας:<br>
    <input type="text" name="title" required>
  </label><br><br>

  <label>
    <input type="checkbox" name="is_public">
    Δημόσια λίστα (ορατή σε άλλους)
  </label><br><br>

  <button type="submit">Δημιουργία</button>
</form>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
