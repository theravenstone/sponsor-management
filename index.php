<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Document</title>
</head>

<body>
    <div class="container auth">
        <h1>Sponsorenverwaltung</h1>
        <?php if (!isset($_SESSION['user_id'])) : ?>
            <div>
                <a href="login.php">Login</a>
                <span> | </span>
                <a href="register.php">Register</a>
            </div>
        <?php else : ?>
            <div>
                <a href="dashboard.php">Dashboard</a>
            </div>
        <?php endif; ?>
    </div>
</body>

</html>