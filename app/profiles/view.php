<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/session.php';

$user_id = $_SESSION['user_id'];

// Φέρνουμε τα στοιχεία του χρήστη
$stmt = $pdo->prepare("SELECT name, surname, username, email FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Φέρνουμε τις λίστες του
$stmt2 = $pdo->prepare("SELECT id, name, is_public FROM playlists WHERE user_id = ?");
$stmt2->execute([$user_id]);
$playlists = $stmt2->fetchAll();
?>

<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <title>Το Προφίλ μου</title>
    <link rel="stylesheet" href="/public/css/styles.css">
</head>
<body>
<?php include '../includes/header.php'; ?>
<main>
    <h2>Καλώς ήρθες, <?= htmlspecialchars($user['name']) ?>!</h2>

    <section>
        <h3>Στοιχεία Λογαριασμού</h3>
        <ul>
            <li><strong>Όνομα:</strong> <?= htmlspecialchars($user['name']) ?></li>
            <li><strong>Επώνυμο:</strong> <?= htmlspecialchars($user['surname']) ?></li>
            <li><strong>Username:</strong> <?= htmlspecialchars($user['username']) ?></li>
            <li><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></li>
        </ul>
        <a href="edit.php">✏️ Επεξεργασία</a> | 
        <a href="delete.php" onclick="return confirm('Είσαι σίγουρος; Αυτή η ενέργεια είναι μη αναστρέψιμη.')">🗑️ Διαγραφή Λογαριασμού</a>
    </section>

    <section>
        <h3>Οι Λίστες μου</h3>
        <?php if (count($playlists) > 0): ?>
            <ul>
                <?php foreach ($playlists as $pl): ?>
                    <li>
                        <a href="../playlists/view.php?id=<?= $pl['id'] ?>">
                            <?= htmlspecialchars($pl['name']) ?>
                        </a>
                        <?= $pl['is_public'] ? '🌐 Δημόσια' : '🔒 Ιδιωτική' ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>Δεν έχεις δημιουργήσει ακόμη λίστες.</p>
        <?php endif; ?>
    </section>
</main>
<?php include '../includes/footer.php'; ?>
</body>
</html>
