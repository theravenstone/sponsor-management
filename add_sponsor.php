<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
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

    // Erstelle den uploads-Ordner für den Sponsor, falls er nicht existiert
    $target_dir = "uploads/$customer_number/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0755, true);
    }

    $logo_path = '';

    if ($_FILES['logo']['name']) {
        $logo_filename = basename($_FILES["logo"]["name"]);
        $logo_path = $target_dir . $logo_filename;
        move_uploaded_file($_FILES["logo"]["tmp_name"], $logo_path);
    }

    $stmt = $pdo->prepare('INSERT INTO sponsors (customer_number, firm_name, contact_last_name, contact_first_name, street, plz, city, country, ust_id, phone, email_contact, email_billing, budget, logo_path, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())');
    if ($stmt->execute([$customer_number, $firm_name, $contact_last_name, $contact_first_name, $street, $plz, $city, $country, $ust_id, $phone, $email_contact, $email_billing, $budget, $logo_path])) {
        // Hole die ID des neu eingefügten Sponsors
        $sponsor_id = $pdo->lastInsertId();

        // Logeintrag vorbereiten
        $log_action = "create";
        $user_id = $_SESSION['user_id'];

        // Prepare statement für das Einfügen in die Logs-Tabelle
        $log_stmt = $pdo->prepare('INSERT INTO logs (user_id, sponsor_id, action, created_at) VALUES (?, ?, ?, NOW())');

        // Führe das Statement für das Logging aus
        if ($log_stmt->execute([$user_id, $sponsor_id, $log_action])) {
            $_SESSION['success'] = 'Sponsor erfolgreich hinzugefügt.';
            header('Location: dashboard.php');
            exit;
        } else {
            $error = 'Fehler beim Erstellen des Logeintrags.';
        }
    } else {
        $error = 'Fehler beim Hinzufügen des Sponsors. Bitte versuchen Sie es erneut.';
    }
}
?>

<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Sponsor hinzufügen</title>
</head>

<body>
    <div>
        <a class="backbutton" href="dashboard.php">Zurück zum Dashboard</a>

        <div class="container action-container">
            <h1>Sponsor hinzufügen</h1>
            <?php if ($error) : ?>
                <p class="error"><?php echo $error; ?></p>
            <?php endif; ?>
            <form method="POST" enctype="multipart/form-data">
                <input type="text" name="customer_number" placeholder="Kundennummer" required>
                <input type="text" name="firm_name" placeholder="Firmenname" required>
                <input type="text" name="contact_last_name" placeholder="Nachname des Ansprechpartners" required>
                <input type="text" name="contact_first_name" placeholder="Vorname des Ansprechpartners" required>
                <input type="text" name="street" placeholder="Straße" required>
                <input type="text" name="plz" placeholder="PLZ" required>
                <input type="text" name="city" placeholder="Ort" required>
                <select name="country" required>
                    <option value="DE">Deutschland</option>
                    <option value="AT">Österreich</option>
                </select>
                <input type="text" name="ust_id" placeholder="USt-ID" required>
                <input type="text" name="phone" placeholder="Telefonnummer des Ansprechpartners">
                <input type="email" name="email_contact" placeholder="E-Mail des Ansprechpartners">
                <input type="email" name="email_billing" placeholder="E-Mail für Rechnungen">
                <input type="number" name="budget" placeholder="Voraussichtliches monatliches Budget in Euro">
                <input type="file" name="logo">
                <button type="submit">Hinzufügen</button>
            </form>
        </div>
    </div>
</body>

</html>