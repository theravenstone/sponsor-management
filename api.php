<?php
header('Content-Type: application/json');
require 'db.php';

// Funktion zum Paginieren der Ergebnisse
function paginateResults($pdo, $table, $perPage, $page) {
    $offset = ($page - 1) * $perPage;
    $stmt = $pdo->query("SELECT COUNT(*) AS total FROM $table");
    $totalResults = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    $totalPages = ceil($totalResults / $perPage);

    $stmt = $pdo->prepare("SELECT * FROM $table ORDER BY created_at DESC LIMIT :perPage OFFSET :offset");
    $stmt->bindParam(':perPage', $perPage, PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    return [
        'total_pages' => $totalPages,
        'current_page' => $page,
        'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)
    ];
}

// Endpoint zur Rückgabe aller Sponsoren mit Paginierung
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['page'])) {
    $perPage = isset($_GET['perPage']) ? intval($_GET['perPage']) : 10;
    $page = max(1, intval($_GET['page']));
    $response = paginateResults($pdo, 'sponsors', $perPage, $page);
    echo json_encode($response);
    exit;
}

// Endpoint zur Rückgabe von Sponsoren anhand der Kundennummer
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['customer_number'])) {
    $customer_number = $_GET['customer_number'];
    $stmt = $pdo->prepare("SELECT * FROM sponsors WHERE customer_number = :customer_number");
    $stmt->bindParam(':customer_number', $customer_number, PDO::PARAM_STR);
    $stmt->execute();
    $sponsors = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($sponsors);
    exit;
}

// Standard-Fehlermeldung für ungültige Anfragen
http_response_code(400);
echo json_encode(array('message' => 'Ungültige Anfrage'));
