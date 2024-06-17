<?php
session_start();
require 'db.php';

if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];

    // Überprüfen, ob beide Passwörter übereinstimmen
    if ($password !== $password_confirm) {
        $error = 'Passwörter stimmen nicht überein.';
    } else {
        // Überprüfen, ob die E-Mail bereits registriert ist
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = 'Diese E-Mail-Adresse ist bereits registriert.';
        } else {
            // Passwort hashen und Benutzer in die Datenbank einfügen
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare('INSERT INTO users (email, password) VALUES (?, ?)');
            if ($stmt->execute([$email, $hashed_password])) {
                $success = 'Registrierung erfolgreich! Sie können sich jetzt einloggen.';
            } else {
                $error = 'Fehler bei der Registrierung. Bitte versuchen Sie es erneut.';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Registrieren</title>
</head>
<body>
    <div class="container auth">
        <h1>Registrieren</h1>
        <?php if ($error): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
        <?php if ($success): ?>
            <p class="success"><?php echo $success; ?></p>
        <?php endif; ?>
        <form method="POST">
            <input type="email" name="email" placeholder="E-Mail" required>
            <input type="password" name="password" placeholder="Passwort" required>
            <input type="password" name="password_confirm" placeholder="Passwort bestätigen" required>
            <button type="submit">Registrieren</button>
        </form>
    </div>
</body>
</html>
