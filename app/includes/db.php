<?php
$host = 'localhost';
$dbname = 'streamzone';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$options = [
  PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
  PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=$charset", $user, $pass, $options);
} catch (PDOException $e) {
    die("Σφάλμα σύνδεσης: " . $e->getMessage());
}
