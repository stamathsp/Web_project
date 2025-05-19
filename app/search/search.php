<?php
require_once '../includes/session.php';
require_once '../includes/db.php';

$filters = [];
$params = [];

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!empty($_GET['q'])) {
        $filters[] = "(p.title LIKE ? OR v.title LIKE ?)";
        $params[] = '%' . $_GET['q'] . '%';
        $params[] = '%' . $_GET['q'] . '%';
    }

    if (!empty($_GET['date_from']) && !empty($_GET['date_to'])) {
        $filters[] = "v.created_at BETWEEN ? AND ?";
        $params[] = $_GET['date_from'];
        $params[] = $_GET['date_to'];
    }

    if (!empty($_GET['user'])) {
        $filters[] = "(u.first_name LIKE ? OR u.last_name LIKE ? OR u.username LIKE ? OR u.email LIKE ?)";
        for ($i = 0; $i < 4; $i++) {
            $params[] = '%' . $_GET['user'] . '%';
        }
    }
}

$where = $filters ? "WHERE " . implode(" AND ", $filters) : "";

$query = "
SELECT p.title AS playlist_title, v.title AS video_title, v.url, v.created_at,
       u.username, u.first_name, u.last_name
FROM playlists p
JOIN videos v ON v.playlist_id = p.id
JOIN users u ON p.user_id = u.id
$where
ORDER BY v.created_at DESC
";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$results = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <title>Αναζήτηση Περιεχομένου</title>
    <link rel="stylesheet" href="/public/css/styles.css">
</head>
<body>
<?php include '../includes/header.php'; ?>
<main>
    <h2>Αναζήτηση Περιεχομένου</h2>

    <form method="GET">
        <input type="text" name="q" placeholder="Τίτλος λίστας ή βίντεο" value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
        <input type="text" name="user" placeholder="Χρήστης (όνομα, username, email)" value="<?= htmlspecialchars($_GET['user'] ?? '') ?>">
        Από <input type="date" name="date_from" value="<?= htmlspecialchars($_GET['date_from'] ?? '') ?>">
        Έως <input type="date" name="date_to" value="<?= htmlspecialchars($_GET['date_to'] ?? '') ?>">
        <button type="submit">Αναζήτηση</button>
    </form>

    <?php if ($_SERVER['REQUEST_METHOD'] === 'GET'): ?>
        <h3>Αποτελέσματα</h3>
        <?php if (count($results) > 0): ?>
            <ul>
                <?php foreach ($results as $r): ?>
                    <li>
                        <strong><?= htmlspecialchars($r['video_title']) ?></strong> από 
                        <?= htmlspecialchars($r['first_name'] . ' ' . $r['last_name']) ?> (<?= htmlspecialchars($r['username']) ?>)
                        στην λίστα: <em><?= htmlspecialchars($r['playlist_title']) ?></em><br>
                        <a href="<?= htmlspecialchars($r['url']) ?>" target="_blank">▶️ Προβολή</a> 
                        | <small><?= htmlspecialchars($r['created_at']) ?></small>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>❌ Δεν βρέθηκαν αποτελέσματα.</p>
        <?php endif; ?>
    <?php endif; ?>
</main>
<?php include '../includes/footer.php'; ?>
</body>
</html>
