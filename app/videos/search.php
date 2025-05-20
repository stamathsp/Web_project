<?php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/oauth.php';

$results = [];
$playlist_id = $_GET['playlist_id'] ?? null;
$query = $_POST['query'] ?? '';
$pageToken = $_POST['pageToken'] ?? null;
$pageIndex = $_POST['pageIndex'] ?? 1;

$nextPageToken = null;
$prevPageToken = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $query !== '') {
    $searchResponse = searchYouTube($query, $pageToken);
    $results = $searchResponse['items'] ?? [];
    $nextPageToken = $searchResponse['nextPageToken'] ?? null;
    $prevPageToken = $searchResponse['prevPageToken'] ?? null;
}
?>

<h2>Αναζήτηση YouTube Βίντεο</h2>

<form method="POST">
  <input type="text" name="query" placeholder="Αναζήτηση..." required value="<?= htmlspecialchars($query) ?>">
  <button type="submit">Αναζήτηση</button>
</form>

<?php if ($results): ?>
  <h3>Αποτελέσματα - Σελίδα <?= $pageIndex ?></h3>
  <ul>
    <?php foreach ($results as $item): ?>
      <li>
        <p><strong><?= htmlspecialchars($item['snippet']['title']) ?></strong></p>
        <iframe width="320" height="180"
                src="https://www.youtube.com/embed/<?= $item['id']['videoId'] ?>"
                frameborder="0" allowfullscreen></iframe>

        <?php if ($playlist_id): ?>
          <form action="add.php" method="POST">
            <input type="hidden" name="playlist_id" value="<?= htmlspecialchars($playlist_id) ?>">
            <input type="hidden" name="video_id" value="<?= htmlspecialchars($item['id']['videoId']) ?>">
            <input type="hidden" name="title" value="<?= htmlspecialchars($item['snippet']['title']) ?>">
            <button type="submit">Προσθήκη σε λίστα</button>
          </form>
        <?php endif; ?>
      </li>
    <?php endforeach; ?>
  </ul>

  <div class="pagination">
    <!-- Προηγούμενη -->
    <?php if ($prevPageToken): ?>
      <form method="POST" style="display:inline">
        <input type="hidden" name="query" value="<?= htmlspecialchars($query) ?>">
        <input type="hidden" name="pageToken" value="<?= htmlspecialchars($prevPageToken) ?>">
        <input type="hidden" name="pageIndex" value="<?= $pageIndex - 1 ?>">
        <button type="submit">⬅️ Προηγούμενη</button>
      </form>
    <?php endif; ?>

    <!-- Επόμενη -->
    <?php if ($nextPageToken): ?>
      <form method="POST" style="display:inline">
        <input type="hidden" name="query" value="<?= htmlspecialchars($query) ?>">
        <input type="hidden" name="pageToken" value="<?= htmlspecialchars($nextPageToken) ?>">
        <input type="hidden" name="pageIndex" value="<?= $pageIndex + 1 ?>">
        <button type="submit">➡️ Επόμενη</button>
      </form>
    <?php endif; ?>
  </div>
<?php endif; ?>

<?php require_once('../includes/footer.php'); ?>
