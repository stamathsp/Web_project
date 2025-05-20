<?php
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/header.php';
?>

<main>
    <h1>Καλωσόρισες στην Πλατφόρμα Περιεχομένου Ροής</h1>

    <?php if (isset($_SESSION['user_id'])): ?>
        <p>Είσαι συνδεδεμένος ως <strong><?= htmlspecialchars($_SESSION['username']) ?></strong>.</p>
        <p><a href="profiles/view.php">➡️ Πήγαινε στο προφίλ σου</a></p>
        <p><a href="playlists/view_all.php">🎵 Δες τις λίστες περιεχομένου</a></p>
    <?php else: ?>
        <p>Κάνε <a href="auth/login.php">Σύνδεση</a> ή <a href="auth/register.php">Εγγραφή</a> για να ξεκινήσεις.</p>
    <?php endif; ?>

    <hr>

    <section>
        <h2>Τι μπορείς να κάνεις εδώ:</h2>
        <ul>
            <li>📺 Δημιουργία και διαχείριση λιστών βίντεο YouTube</li>
            <li>🔍 Αναζήτηση περιεχομένου και χρηστών</li>
            <li>🌐 Ακολουθία και προβολή δημόσιων λιστών</li>
            <li>🧾 Εξαγωγή δεδομένων σε YAML</li>
        </ul>
    </section>
</main>

<?php
require_once 'includes/footer.php';
?>