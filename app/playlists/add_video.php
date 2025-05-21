<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/session.php';
require_once realpath(__DIR__ . '/../includes/secrets.php');


$playlist_id = $_GET['id'] ?? null;
$user_id = $_SESSION['user_id'];

if (!$playlist_id) {
    die("❌ Δεν δόθηκε ID λίστας.");
}

// Έλεγχος αν η λίστα ανήκει στον τρέχοντα χρήστη
$stmt = $pdo->prepare("SELECT * FROM playlists WHERE id = ? AND user_id = ?");
$stmt->execute([$playlist_id, $user_id]);
$playlist = $stmt->fetch();

if (!$playlist) {
    die("🚫 Δεν έχεις πρόσβαση σε αυτή τη λίστα.");
}

// Χειρισμός αναζήτησης YouTube
$results = [];
if (isset($_GET['q']) && !empty(trim($_GET['q']))) {
    $query = $_GET['q'];
    $apiKey = YOUTUBE_API_KEY;

    $url = "https://www.googleapis.com/youtube/v3/search?part=snippet&q=" . urlencode($query) . "&type=video&maxResults=5&key=" . $apiKey;

    $response = @file_get_contents($url);
    if ($response === false) {
        die("❌ Η αναζήτηση στο YouTube απέτυχε. Έλεγξε το API key ή τη σύνδεση.");
    }

    $data = json_decode($response, true);
    $results = $data['items'] ?? [];
}

// Χειρισμός προσθήκης video στη βάση
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['video_id'], $_POST['title'])) {
    $video_id = $_POST['video_id'];
    $title = $_POST['title'];

    $stmt = $pdo->prepare("INSERT INTO playlist_videos (playlist_id, user_id, youtube_video_id, title) VALUES (?, ?, ?, ?)");
    $stmt->execute([$playlist_id, $user_id, $video_id, $title]);

    header("Location: view.php?id=$playlist_id");
    exit;
}
?>

<?php require_once __DIR__ . '/../includes/header.php'; ?>

<h2>➕ Προσθήκη video στη λίστα: <?= htmlspecialchars($playlist['name']) ?></h2>

<!-- Φόρμα αναζήτησης -->
<form method="get">
    <input type="hidden" name="id" value="<?= $playlist_id ?>">
    <input type="text" name="q" placeholder="Αναζήτηση στο YouTube..." required>
    <button type="submit">🔍 Αναζήτηση</button>
</form>

<!-- Αποτελέσματα αναζήτησης -->
<?php if (!empty($results)): ?>
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
                <button type="submit">➕ Προσθήκη στη λίστα</button>
            </form>
        </li>
        <?php endforeach; ?>
    </ul>
<?php elseif (isset($_GET['q'])): ?>
    <p>❗ Δεν βρέθηκαν αποτελέσματα για "<?= htmlspecialchars($_GET['q']) ?>".</p>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
