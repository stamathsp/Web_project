<?php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/oauth.php'; // έχει το API Key και τη συνάρτηση searchYouTube()

$results = [];
$playlist_id = $_GET['playlist_id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $query = $_POST['query'] ?? '';
    $results = searchYouTube($query);
}
?>

<h2>Αναζήτηση YouTube Βίντεο</h2>

<form method="POST">
  <input type="text" name="query" placeholder="Αναζήτηση..." required>
  <button type="submit">Αναζήτηση</button>
</form>

<?php if ($results): ?>
  <h3>Αποτελέσματα:</h3>
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
<?php endif; ?>

<?php require_once('../includes/footer.php'); ?>
