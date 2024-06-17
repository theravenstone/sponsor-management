<?php
session_start();
require 'db.php';
require 'config.php'; // Einbinden der Konfigurationsdatei

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Pagination
$perPage = defined('PAGE_SIZE_SPONSORS') ? PAGE_SIZE_SPONSORS : 10; // Laden der PAGE_SIZE_SPONSORS aus config.php, Standardwert 10
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

$offset = ($page - 1) * $perPage;

// Query für aktive Sponsoren mit Pagination und Suche
$whereClause = ' WHERE archived = 0'; // Nur nicht archivierte Sponsoren anzeigen
$params = [];

if (!empty($search)) {
    $whereClause .= ' AND (customer_number LIKE ? OR firm_name LIKE ? OR CONCAT(contact_first_name, " ", contact_last_name) LIKE ?)';
    $params = ["%$search%", "%$search%", "%$search%"];
}

$countStmt = $pdo->prepare('SELECT COUNT(*) AS total FROM sponsors' . $whereClause);
$countStmt->execute($params);
$totalResults = $countStmt->fetchColumn();

$totalPages = ceil($totalResults / $perPage);

$stmt = $pdo->prepare('SELECT * FROM sponsors' . $whereClause . ' ORDER BY created_at DESC LIMIT ? OFFSET ?');
$stmt->execute(array_merge($params, [$perPage, $offset]));
$sponsors = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Sponsorenverwaltung</title>
</head>

<body>
    <div class="container">
        <div class="actions">
            <h1>Sponsorenverwaltung</h1>
            <div>
                <a href="logout.php">Abmelden</a>
            </div>
        </div>

        <?php if (isset($_SESSION['success'])) : ?>
            <div class="message success">
                <?php echo $_SESSION['success']; ?>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])) : ?>
            <div class="message error">
                <?php echo $_SESSION['error']; ?>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <div class="actions">
            <div class="search">
                <form action="dashboard.php" method="GET">
                    <input type="text" name="search" placeholder="Suche nach Kundennummer, Firmenname oder Ansprechpartner" value="<?php echo htmlspecialchars($search); ?>">
                    <button type="submit">Suchen</button>
                </form>
            </div>
            <div>
                <a href="logs.php">Logs</a>
                <span> | </span>
                <a href="archive.php">Archiviert</a>
                <span> | </span>
                <a href="add_sponsor.php">Neuen Sponsor hinzufügen</a>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Kundennummer</th>
                    <th>Firmenname</th>
                    <th>Ansprechpartner</th>
                    <th>Logo</th>
                    <th>Zustand</th>
                    <th>Aktionen</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sponsors as $sponsor) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($sponsor['customer_number']); ?></td>
                        <td><?php echo htmlspecialchars($sponsor['firm_name']); ?></td>
                        <td><?php echo htmlspecialchars($sponsor['contact_first_name'] . ' ' . $sponsor['contact_last_name']); ?></td>
                        <td>
                            <?php if (!empty($sponsor['logo_path']) && file_exists($sponsor['logo_path'])) : ?>
                                <img src="<?php echo $sponsor['logo_path']; ?>" alt="Logo" style="max-width: 100px; max-height: 50px;">
                            <?php else : ?>
                                Kein Logo vorhanden
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($sponsor['locked']) : ?>
                                <span style="color: red;">Gesperrt</span>
                            <?php else : ?>
                                <span style="color: green;">Entsperrt</span>
                            <?php endif; ?>
                        </td>

                        <td>
                            <?php if (!$sponsor['locked']) : ?>
                                <form action="edit_sponsor.php" method="GET">
                                    <input type="hidden" name="id" value="<?php echo $sponsor['id']; ?>">
                                    <button type="submit">Bearbeiten</button>
                                </form>
                                <form action="toggle_lock.php" method="POST">
                                    <input type="hidden" name="id" value="<?php echo $sponsor['id']; ?>">
                                    <button type="submit">Sperren</button>
                                </form>
                            <?php else : ?>
                                <form action="toggle_lock.php" method="POST">
                                    <input type="hidden" name="id" value="<?php echo $sponsor['id']; ?>">
                                    <button type="submit">Entsperren</button>
                                </form>
                            <?php endif; ?>
                            <?php if ($sponsor['archived'] == 0) : ?>
                                <form action="toggle_archive.php" method="POST">
                                    <input type="hidden" name="id" value="<?php echo $sponsor['id']; ?>">
                                    <button type="submit">Archivieren</button>
                                </form>
                            <?php else : ?>
                                <form action="toggle_archive.php" method="POST">
                                    <input type="hidden" name="id" value="<?php echo $sponsor['id']; ?>">
                                    <button type="submit">Wiederherstellen</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="pagination">
            <?php if ($page > 1) : ?>
                <a href="?page=<?php echo ($page - 1); ?>">Zurück</a>
            <?php endif; ?>
            <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
                <a href="?page=<?php echo $i . '&search=' . urlencode($search); ?>" <?php if ($i === $page) echo 'class="active"'; ?>><?php echo $i; ?></a>
            <?php endfor; ?>
            <?php if ($page < $totalPages) : ?>
                <a href="?page=<?php echo ($page + 1); ?>">Weiter</a>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>