<?php
session_start();

$protected_pages = [
    '/profiles/view.php',
    '/profiles/edit.php',
    '/profiles/delete.php',
    '/playlists/create.php',
    '/playlists/edit.php',
    '/videos/add.php',
    '/videos/play.php',
    '/search/search.php',
];

$current_page = $_SERVER['PHP_SELF'];

if (in_array($current_page, $protected_pages) && !isset($_SESSION['user_id'])) {
    $_SESSION['redirect_to'] = $_SERVER['REQUEST_URI'];
    header("Location: /auth/login.php");
    exit;
}
