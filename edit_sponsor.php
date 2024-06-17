<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $customer_number = $_POST['customer_number'];
    $firm_name = $_POST['firm_name'];
    $contact_last_name = $_POST['contact_last_name'];
    $contact_first_name = $_POST['contact_first_name'];
    $street = $_POST['street'];
    $plz = $_POST['plz'];
    $city = $_POST['city'];
    $country = $_POST['country'];
    $ust_id = $_POST['ust_id'];
    $phone = $_POST['phone'];
    $email_contact = $_POST['email_contact'];
    $email_billing = $_POST['email_billing'];
    $budget = $_POST['budget'];
    $locked = isset($_POST['locked']) ? 1 : 0;

    $stmt = $pdo->prepare('UPDATE sponsors SET customer_number = ?, firm_name = ?, contact_last_name = ?, contact_first_name = ?, street = ?, plz = ?, city = ?, country = ?, ust_id = ?, phone = ?, email_contact = ?, email_billing = ?, budget = ?, locked = ?, updated_at = NOW() WHERE id = ?');
    if ($stmt->execute([$customer_number, $firm_name, $contact_last_name, $contact_first_name, $street, $plz, $city, $country, $ust_id, $phone, $email_contact, $email_billing, $budget, $locked, $id])) {
        // Hole die ID des Sponsors
        $sponsor_id = $id;

        // Logeintrag vorbereiten
        $log_action = "update";
        $user_id = $_SESSION['user_id'];

        // Prepare statement für das Einfügen in die Logs-Tabelle
        $log_stmt = $pdo->prepare('INSERT INTO logs (user_id, sponsor_id, action, created_at) VALUES (?, ?, ?, NOW())');

        // Führe das Statement für das Logging aus
        if ($log_stmt->execute([$user_id, $sponsor_id, $log_action])) {
            $_SESSION['success'] = 'Sponsor erfolgreich aktualisiert.';
            header('Location: dashboard.php');
            exit;
        } else {
            $error = 'Fehler beim Erstellen des Logeintrags.';
        }
    } else {
        $_SESSION['error'] = 'Fehler beim Aktualisieren des Sponsors. Bitte versuchen Sie es erneut.';
    }
} else {
    // Laden der vorhandenen Daten des Sponsors
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $stmt = $pdo->prepare('SELECT * FROM sponsors WHERE id = ?');
        $stmt->execute([$id]);
        $sponsor = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$sponsor) {
            header('Location: dashboard.php');
            exit;
        }
    } else {
        header('Location: dashboard.php');
        exit;
    }
}
?>


<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Sponsor bearbeiten</title>

</head>

<body>
    <div>
        <a class="backbutton" href="dashboard.php">Zurück zum Dashboard</a>
        <div class="container">
            <h1>Sponsor bearbeiten</h1>
            <?php if ($error) : ?>
                <p class="error"><?php echo $error; ?></p>
            <?php endif; ?>
            <?php if ($success) : ?>
                <p class="success"><?php echo $success; ?></p>
            <?php endif; ?>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($sponsor['id']); ?>">
                <input type="text" name="customer_number" value="<?php echo htmlspecialchars($sponsor['customer_number']); ?>" placeholder="Kundennummer" required>
                <input type="text" name="firm_name" value="<?php echo htmlspecialchars($sponsor['firm_name']); ?>" placeholder="Firmenname" required>
                <input type="text" name="contact_last_name" value="<?php echo htmlspecialchars($sponsor['contact_last_name']); ?>" placeholder="Nachname des Ansprechpartners" required>
                <input type="text" name="contact_first_name" value="<?php echo htmlspecialchars($sponsor['contact_first_name']); ?>" placeholder="Vorname des Ansprechpartners" required>
                <input type="text" name="street" value="<?php echo htmlspecialchars($sponsor['street']); ?>" placeholder="Straße" required>
                <input type="text" name="plz" value="<?php echo htmlspecialchars($sponsor['plz']); ?>" placeholder="PLZ" required>
                <input type="text" name="city" value="<?php echo htmlspecialchars($sponsor['city']); ?>" placeholder="Ort" required>
                <select name="country" required>
                    <option value="DE" <?php if ($sponsor['country'] == 'DE') echo 'selected'; ?>>Deutschland</option>
                    <option value="AT" <?php if ($sponsor['country'] == 'AT') echo 'selected'; ?>>Österreich</option>
                </select>
                <input type="text" name="ust_id" value="<?php echo htmlspecialchars($sponsor['ust_id']); ?>" placeholder="USt-ID" required>
                <input type="text" name="phone" value="<?php echo htmlspecialchars($sponsor['phone']); ?>" placeholder="Telefonnummer des Ansprechpartners">
                <input type="email" name="email_contact" value="<?php echo htmlspecialchars($sponsor['email_contact']); ?>" placeholder="E-Mail des Ansprechpartners">
                <input type="email" name="email_billing" value="<?php echo htmlspecialchars($sponsor['email_billing']); ?>" placeholder="E-Mail für Rechnungen">
                <input type="number" name="budget" value="<?php echo htmlspecialchars($sponsor['budget']); ?>" placeholder="Voraussichtliches monatliches Budget in Euro">
                <label>Aktuelles Logo:</label>
                <?php if (!empty($sponsor['logo_path']) && file_exists($sponsor['logo_path'])) : ?>
                    <img src="<?php echo $sponsor['logo_path']; ?>" alt="Logo" style="max-width: 100px; max-height: 50px;">
                <?php else : ?>
                    Kein Logo vorhanden
                <?php endif; ?>
                <input type="file" name="logo">
                <button type="submit">Speichern</button>
            </form>
        </div>
    </div>
</body>

</html>