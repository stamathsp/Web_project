<?php
require_once __DIR__ . '/../includes/db.php'; // PDO σύνδεση
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Έλεγχοι εγκυρότητας
    if (empty($first_name) || empty($last_name) || empty($username) || empty($email) || empty($password)) {
        $errors[] = "Όλα τα πεδία είναι υποχρεωτικά.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Μη έγκυρη διεύθυνση email.";
    } else {
        // Έλεγχος μοναδικότητας username/email
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if ($stmt->fetchColumn() > 0) {
            $errors[] = "Το username ή το email χρησιμοποιείται ήδη.";
        }
    }

    // Αν δεν υπάρχουν σφάλματα, εισαγωγή στη βάση
    if (empty($errors)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (name, surname, username, email, password) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$first_name, $last_name, $username, $email, $hashedPassword]);
        $success = true;
    }
}
?>

<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <title>Εγγραφή Χρήστη</title>
    <link rel="stylesheet" href="/public/css/styles.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <main>
        <h2>Εγγραφή</h2>

        <?php if (!empty($errors)): ?>
            <div class="error">
                <ul><?php foreach ($errors as $e) echo "<li>$e</li>"; ?></ul>
            </div>
        <?php elseif ($success): ?>
            <div class="success">Η εγγραφή ολοκληρώθηκε! <a href="login.php">Συνδεθείτε</a></div>
        <?php endif; ?>

        <form method="POST">
            <label>Όνομα: <input type="text" name="first_name" required></label><br>
            <label>Επώνυμο: <input type="text" name="last_name" required></label><br>
            <label>Username: <input type="text" name="username" required></label><br>
            <label>Email: <input type="email" name="email" required></label><br>
            <label>Κωδικός: <input type="password" name="password" required></label><br>
            <button type="submit">Εγγραφή</button>
        </form>
    </main>
    <?php include '../includes/footer.php'; ?>
</body>
</html>