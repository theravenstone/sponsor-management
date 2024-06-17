<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];

    // Query to fetch current locked status
    $stmt = $pdo->prepare('SELECT locked FROM sponsors WHERE id = ?');
    $stmt->execute([$id]);
    $locked = $stmt->fetchColumn();

    // Toggle locked status
    $newLocked = $locked ? 0 : 1;

    // Update locked status in database
    $updateStmt = $pdo->prepare('UPDATE sponsors SET locked = ? WHERE id = ?');
    if ($updateStmt->execute([$newLocked, $id])) {
        // Hole die ID des Sponsors
        $sponsor_id = $id;

        // Logeintrag vorbereiten
        $log_action = $newLocked ? 'lock' : 'unlock';
        $user_id = $_SESSION['user_id'];

        // Prepare statement für das Einfügen in die Logs-Tabelle
        $log_stmt = $pdo->prepare('INSERT INTO logs (user_id, sponsor_id, action, created_at) VALUES (?, ?, ?, NOW())');

        // Führe das Statement für das Logging aus
        if ($log_stmt->execute([$user_id, $sponsor_id, $log_action])) {
            $_SESSION['success'] = 'Sponsor erfolgreich ' . ($newLocked ? 'gesperrt.' : 'entsperrt.');
            header('Location: dashboard.php');
            exit;
        } else {
            $error = 'Fehler beim Erstellen des Logeintrags.';
        }
    } else {
        $_SESSION['error'] = 'Fehler beim sperren/entsperren des Sponsors. Bitte versuchen Sie es erneut.';
    }
} else {
    $_SESSION['error'] = 'Ungültige Anfrage zur sperrung/entsperrung des Sponsors.';
    header('Location: dashboard.php');
    exit;
}
