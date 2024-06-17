<?php
session_start();
require 'db.php';
require 'config.php'; // Einbinden der Konfigurationsdatei

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}


// Pagination
$perPage = defined('PAGE_SIZE_LOGS') ? PAGE_SIZE_LOGS : 10;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;

$offset = ($page - 1) * $perPage;

// Query für die Logs mit Pagination
$countStmt = $pdo->query('SELECT COUNT(*) AS total FROM logs');
$totalResults = $countStmt->fetchColumn();

$totalPages = ceil($totalResults / $perPage);

$stmt = $pdo->prepare('SELECT logs.*, users.username AS user_name, sponsors.firm_name AS sponsor_name
                      FROM logs
                      LEFT JOIN users ON logs.user_id = users.id
                      LEFT JOIN sponsors ON logs.sponsor_id = sponsors.id
                      ORDER BY created_at DESC
                      LIMIT ? OFFSET ?');
$stmt->execute([$perPage, $offset]);
$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Logs anzeigen</title>
</head>

<body>
    <div>
        <a class="backbutton" href="dashboard.php">Zurück zum Dashboard</a>

        <div class="container">
            <h1>Logs anzeigen</h1>
            <div class="logs">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nutzername</th>
                            <th>Sponsor</th>
                            <th>Aktion</th>
                            <th>Erstellt am</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($logs as $log) : ?>
                            <tr>
                                <td><?php echo $log['id']; ?></td>
                                <td><?php echo $log['user_name']; ?></td>
                                <td><?php echo $log['sponsor_name']; ?></td>
                                <td><?php echo $log['action']; ?></td>
                                <td><?php echo $log['created_at']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <!-- Pagination -->
                <?php if ($totalPages > 1) : ?>
                    <div class="pagination">
                        <?php if ($page > 1) : ?>
                            <a href="?page=<?php echo ($page - 1); ?>">Zurück</a>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
                            <a href="?page=<?php echo $i; ?>" <?php if ($i === $page) echo 'class="active"'; ?>><?php echo $i; ?></a>
                        <?php endfor; ?>

                        <?php if ($page < $totalPages) : ?>
                            <a href="?page=<?php echo ($page + 1); ?>">Weiter</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>

</html>
