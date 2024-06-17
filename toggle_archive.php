<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];

    // Query to fetch current archived status
    $stmt = $pdo->prepare('SELECT archived FROM sponsors WHERE id = ?');
    $stmt->execute([$id]);
    $archived = $stmt->fetchColumn();

    // Toggle archived status
    $newArchived = $archived ? 0 : 1;

    // Update archived status in database
    $updateStmt = $pdo->prepare('UPDATE sponsors SET archived = ? WHERE id = ?');
    if ($updateStmt->execute([$newArchived, $id])) {
        // Hole die ID des Sponsors
        $sponsor_id = $id;

        // Logeintrag vorbereiten
        $log_action = $newArchived ? 'archive' : 'restore';
        $user_id = $_SESSION['user_id'];

        // Prepare statement für das Einfügen in die Logs-Tabelle
        $log_stmt = $pdo->prepare('INSERT INTO logs (user_id, sponsor_id, action, created_at) VALUES (?, ?, ?, NOW())');

        // Führe das Statement für das Logging aus
        if ($log_stmt->execute([$user_id, $sponsor_id, $log_action])) {
            $_SESSION['success'] = 'Sponsor erfolgreich ' . ($newArchived ? 'archiviert.' : 'wiederhergestellt.');
            if ($newArchived) {
                header('Location: archive.php');
            } else {
                header('Location: dashboard.php');
            }
            exit;
        } else {
            $error = 'Fehler beim Erstellen des Logeintrags.';
        }
    } else {
        $_SESSION['error'] = 'Fehler beim archivieren/wiederherstellen des Sponsors. Bitte versuchen Sie es erneut.';
    }
} else {
    $_SESSION['error'] = 'Ungültige Anfrage zur archivierung/wiederherstellung des Sponsors.';
    header('Location: dashboard.php');
    exit;
}
