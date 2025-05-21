<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/secrets.php';
require_once __DIR__ . '/../includes/header.php';

$playlist_id = $_GET['id'] ?? null;
$user_id = $_SESSION['user_id'];

if (!$playlist_id) {
    die("❌ Δεν δόθηκε ID λίστας.");
}

// Επιβεβαίωση ιδιοκτησίας playlist
$stmt = $pdo->prepare("SELECT * FROM playlists WHERE id = ? AND user_id = ?");
$stmt->execute([$playlist_id, $user_id]);
$playlist = $stmt->fetch();

if (!$playlist) {
    die("🚫 Δεν έχεις πρόσβαση σε αυτή τη λίστα.");
}

// --- Χειρισμός POST για προσθήκη βίντεο ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_video'])) {
    $video_id = $_POST['video_id'];
    $title = $_POST['title'];

    $stmt = $pdo->prepare("INSERT INTO playlist_videos (playlist_id, user_id, youtube_video_id, title) VALUES (?, ?, ?, ?)");
    $stmt->execute([$playlist_id, $user_id, $video_id, $title]);
}

// --- Χειρισμός αναζήτησης ---
$query = $_GET['q'] ?? '';
$pageToken = $_GET['pageToken'] ?? null;
$pageIndex = $_GET['pageIndex'] ?? 1;

$results = [];
$nextPageToken = null;
$prevPageToken = null;

if ($query !== '') {
    $apiKey = YOUTUBE_API_KEY;
    $baseUrl = "https://www.googleapis.com/youtube/v3/search?part=snippet&type=video&maxResults=5";
    $url = $baseUrl . "&q=" . urlencode($query) . "&key=" . $apiKey;
    if ($pageToken) {
        $url .= "&pageToken=" . urlencode($pageToken);
    }

    $json = @file_get_contents($url);
    if ($json !== false) {
        $data = json_decode($json, true);
        $results = $data['items'] ?? [];
        $nextPageToken = $data['nextPageToken'] ?? null;
        $prevPageToken = $data['prevPageToken'] ?? null;
    }
}
?>

<h2>➕ Προσθήκη video στη λίστα: <?= htmlspecialchars($playlist['name']) ?></h2>

<!-- Φόρμα αναζήτησης -->
<form method="get">
    <input type="hidden" name="id" value="<?= $playlist_id ?>">
    <input type="text" name="q" placeholder="Αναζήτηση στο YouTube..." required value="<?= htmlspecialchars($query) ?>">
    <button type="submit">🔍 Αναζήτηση</button>
</form>

<?php if (!empty($results)): ?>
    <h3>Αποτελέσματα - Σελίδα <?= $pageIndex ?></h3>
    <ul>
        <?php foreach ($results as $item): 
            $vid = $item['id']['videoId'];
            $title = $item['snippet']['title'];
        ?>
        <li style="margin-bottom: 1rem;">
            <strong><?= htmlspecialchars($title) ?></strong><br>
            <img src="<?= $item['snippet']['thumbnails']['default']['url'] ?>" alt="Thumbnail"><br>
            <form method="post" style="display:inline;">
                <input type="hidden" name="video_id" value="<?= $vid ?>">
                <input type="hidden" name="title" value="<?= htmlspecialchars($title) ?>">
                <input type="hidden" name="add_video" value="1">
                <button type="submit">➕ Προσθήκη στη λίστα</button>
            </form>
        </li>
        <?php endforeach; ?>
    </ul>

    <!-- Σελιδοποίηση -->
    <div class="pagination">
        <?php if ($prevPageToken): ?>
            <a href="?id=<?= $playlist_id ?>&q=<?= urlencode($query) ?>&pageToken=<?= $prevPageToken ?>&pageIndex=<?= $pageIndex - 1 ?>">⬅️ Προηγούμενη</a>
        <?php endif; ?>

        <?php if ($nextPageToken): ?>
            <a href="?id=<?= $playlist_id ?>&q=<?= urlencode($query) ?>&pageToken=<?= $nextPageToken ?>&pageIndex=<?= $pageIndex + 1 ?>">➡️ Επόμενη</a>
        <?php endif; ?>
    </div>
<?php elseif ($query !== ''): ?>
    <p>❗ Δεν βρέθηκαν αποτελέσματα για "<?= htmlspecialchars($query) ?>".</p>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
