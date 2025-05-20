<?php
require_once __DIR__ . '/../includes/header.php';

$youtube_id = $_GET['id'] ?? null;

if (!$youtube_id) {
    echo "<p>Δεν ορίστηκε video.</p>";
    require_once('../includes/footer.php');
    exit;
}
?>

<h2>Αναπαραγωγή Video</h2>

<div style="text-align: center; margin-top: 2rem;">
  <iframe width="560" height="315"
          src="https://www.youtube.com/embed/<?= htmlspecialchars($youtube_id) ?>"
          frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
          allowfullscreen>
  </iframe>
</div>

<?php require_once('../includes/footer.php'); ?>
