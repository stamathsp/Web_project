<?php
?>
<!DOCTYPE html>
<html lang="el">
<head>
  <meta charset="UTF-8">
  <title>StreamZone</title>
  <link rel="stylesheet" href="/public/css/styles.css">
  <script src="/public/js/theme.js" defer></script>
  <script src="/public/js/accordion.js" defer></script


</head>

<body>
  
<header>
  <button id="theme-toggle">🌓</button>
  <nav>
    <a href="/index.php">Αρχική</a>
    <a href="/about.php">Σκοπός</a>
    <a href="/help.php">Βοήθεια</a>
    <?php if (isset($_SESSION['user_id'])): ?>
      <a href="/profiles/view.php">Το Προφίλ μου</a>
      <a href="/auth/logout.php">Έξοδος</a>
    <?php else: ?>
      <a href="/auth/register.php">Εγγραφή</a>
      <a href="/auth/login.php">Σύνδεση</a>
    <?php endif; ?>
  </nav>
</header>
<main>
