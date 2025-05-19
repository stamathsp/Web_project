<?php
require_once '../includes/session.php';
require_once '../includes/db.php';

$user_id = $_SESSION['user_id'];
$errors = [];
$success = false;

// Φέρνουμε τα στοιχεία για προγεμισμένα πεδία
$stmt = $pdo->prepare("SELECT first_name, last_name, email, password FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];

    if (empty($first_name) || empty($last_name) || empty($email) || empty($current_password)) {
        $errors[] = "Τα πεδία με * είναι υποχρεωτικά.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Μη έγκυρο email.";
    } elseif (!password_verify($current_password, $user['password'])) {
        $errors[] = "Ο τρέχων κωδικός είναι λανθασμένος.";
    } else {
        // Αν όλα οκ, ενημερώνουμε
        $params = [$first_name, $last_name, $email];

        if (!empty($new_password)) {
            $hashed = password_hash($new_password, PASSWORD_DEFAULT);
            $sql = "UPDATE users SET first_name = ?, last_name = ?, email = ?, password = ? WHERE id = ?";
            $params[] = $hashed;
        } else {
            $sql = "UPDATE users SET first_name = ?, last_name = ?, email = ? WHERE id = ?";
        }

        $params[] = $user_id;
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $success = true;
    }
}
?>

<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <title>Επεξεργασία Προφίλ</title>
    <link rel="stylesheet" href="/public/css/styles.css">
</head>
<body>
<?php include '../includes/header.php'; ?>
<main>
    <h2>Επεξεργασία Προφίλ</h2>

    <?php if (!empty($errors)): ?>
        <div class="error">
            <ul><?php foreach ($errors as $e) echo "<li>$e</li>"; ?></ul>
        </div>
    <?php elseif ($success): ?>
        <div class="success">Τα στοιχεία ενημερώθηκαν με επιτυχία!</div>
    <?php endif; ?>

    <form method="POST">
        <label>Όνομα*: <input type="text" name="first_name" value="<?= htmlspecialchars($user['first_name']) ?>" required></label><br>
        <label>Επώνυμο*: <input type="text" name="last_name" value="<?= htmlspecialchars($user['last_name']) ?>" required></label><br>
        <label>Email*: <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required></label><br>
        <label>Τρέχων Κωδικός*: <input type="password" name="current_password" required></label><br>
        <label>Νέος Κωδικός (αν θέλεις): <input type="password" name="new_password"></label><br>
        <button type="submit">Αποθήκευση</button>
    </form>
</main>
<?php include '../includes/footer.php'; ?>
</body>
</html>
