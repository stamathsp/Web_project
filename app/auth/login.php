<?php
session_start();
require_once '../includes/db.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $errors[] = "Συμπληρώστε και τα δύο πεδία.";
    } else {
        // Έλεγχος χρήστη
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Επιτυχής σύνδεση
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header("Location: ../index.php"); // ή όπου θες να πας
            exit;
        } else {
            $errors[] = "Λάθος στοιχεία σύνδεσης.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <title>Σύνδεση</title>
    <link rel="stylesheet" href="/public/css/styles.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <main>
        <h2>Σύνδεση</h2>

        <?php if (!empty($errors)): ?>
            <div class="error">
                <ul><?php foreach ($errors as $e) echo "<li>$e</li>"; ?></ul>
            </div>
        <?php endif; ?>

        <form method="POST">
            <label>Username: <input type="text" name="username" required></label><br>
            <label>Κωδικός: <input type="password" name="password" required></label><br>
            <button type="submit">Σύνδεση</button>
        </form>
    </main>
    <?php include '../includes/footer.php'; ?>
</body>
</html>