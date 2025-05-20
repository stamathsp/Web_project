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

    <section>
        <h2>Συχνές Ερωτήσεις</h2>
        <div class="accordion">
  <button class="accordion-toggle">❓ Τι είναι το StreamLists;</button>
  <div class="accordion-content">
    <p>Η πλατφόρμα μας επιτρέπει να δημιουργείς και να μοιράζεσαι λίστες βίντεο από το YouTube.</p>
  </div>

  <button class="accordion-toggle">🔒 Είναι ασφαλές;</button>
  <div class="accordion-content">
    <p>Ναι, τα προσωπικά δεδομένα προστατεύονται και χρησιμοποιούμε ασφαλείς μεθόδους σύνδεσης.</p>
  </div>

  <button class="accordion-toggle">⚙️ Πώς να αλλάξω θέμα;</button>
  <div class="accordion-content">
    <p>Χρησιμοποίησε το κουμπί αλλαγής θέματος πάνω δεξιά.</p>
  </div>
</div>

    </section>
</main>

<?php
require_once 'includes/footer.php';
?>
