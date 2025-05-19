<?php
require_once '../app/includes/db.php';

echo "<h2>✅ Έλεγχος Σύνδεσης με Βάση</h2>";

try {
    $stmt = $pdo->query("SELECT 1");
    echo "<p>🟢 Η σύνδεση με τη βάση δεδομένων λειτουργεί σωστά.</p>";
} catch (PDOException $e) {
    echo "<p>🔴 Σφάλμα στη σύνδεση: " . $e->getMessage() . "</p>";
    exit;
}

// Πίνακες προς έλεγχο
$tables = ['users', 'playlists', 'videos', 'follows'];

echo "<h2>📦 Έλεγχος Βασικών Πινάκων</h2><ul>";
foreach ($tables as $table) {
    try {
        $stmt = $pdo->query("SELECT COUNT(*) FROM `$table`");
        $count = $stmt->fetchColumn();
        echo "<li>✅ Πίνακας <strong>$table</strong> υπάρχει και έχει <strong>$count</strong> εγγραφές.</li>";
    } catch (PDOException $e) {
        echo "<li>⚠️ Ο πίνακας <strong>$table</strong> δεν βρέθηκε ή είχε σφάλμα: " . $e->getMessage() . "</li>";
    }
}
echo "</ul>";

// Δείγμα χρηστών
echo "<h2>👤 Δείγμα Χρηστών</h2>";
try {
    $stmt = $pdo->query("SELECT id, username, email FROM users LIMIT 5");
    $users = $stmt->fetchAll();
    if ($users) {
        echo "<ul>";
        foreach ($users as $u) {
            echo "<li>[{$u['id']}] {$u['username']} ({$u['email']})</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>⚠️ Δεν υπάρχουν χρήστες στη βάση.</p>";
    }
} catch (PDOException $e) {
    echo "<p>🔴 Σφάλμα: " . $e->getMessage() . "</p>";
}
?>
