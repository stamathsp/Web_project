<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/session.php';

$user_id = $_SESSION['user_id'];
$errors = [];
$success = false;

// Φέρνουμε τα στοιχεία για προγεμισμένα πεδία
$stmt = $pdo->prepare("SELECT name, surname, email, password FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $surname = trim($_POST['surname']);
    $email = trim($_POST['email']);
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];

    if (empty($name) || empty($surname) || empty($email) || empty($current_password)) {
        $errors[] = "Τα πεδία με * είναι υποχρεωτικά.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Μη έγκυρο email.";
    } elseif (!password_verify($current_password, $user['password'])) {
        $errors[] = "Ο τρέχων κωδικός είναι λανθασμένος.";
    } else {
        $params = [$name, $surname, $email];

        if (!empty($new_password)) {
            $hashed = password_hash($new_password, PASSWORD_DEFAULT);
            $sql = "UPDATE users SET name = ?, surname = ?, email = ?, password = ? WHERE id = ?";
            $params[] = $hashed;
        } else {
            $sql = "UPDATE users SET name = ?, surname = ?, email = ? WHERE id = ?";
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
        <label>Όνομα*: <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required></label><br>
        <label>Επώνυμο*: <input type="text" name="surname" value="<?= htmlspecialchars($user['surname']) ?>" required></label><br>
        <label>Email*: <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required></label><br>
        <label>Τρέχων Κωδικός*: <input type="password" name="current_password" required></label><br>
        <label>Νέος Κωδικός (αν θέλεις): <input type="password" name="new_password"></label><br>
        <button type="submit">Αποθήκευση</button>
    </form>
</main>
<?php include '../includes/footer.php'; ?>
</body>
</html>
