<?php
require_once 'auth.php';
require_once __DIR__ . '/../config/db.php';

// Active players
$stmt = $pdo->prepare("
    SELECT * FROM floorball_players
    WHERE is_active = 1
    ORDER BY name
");
$stmt->execute();
$activePlayers = $stmt->fetchAll();

// Inactive players
$stmt = $pdo->prepare("
    SELECT * FROM floorball_players
    WHERE is_active = 0
    ORDER BY name
");
$stmt->execute();
$inactivePlayers = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="da">

<head>
    <meta charset="UTF-8">
    <title>Admin – Motions Floorball</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../css/app.css">
</head>
<body>

<div class="admin-top">
    <a class="btn btn-toggle" href="logout.php">Log ud</a>
</div>

<h1>Admin – Spillere</h1>

<div class="admin-actions">
    <a class="btn btn-edit" href="player_form.php">➕ Ny spiller</a>
</div>


<h2>Aktive spillere</h2>

<?php if (empty($activePlayers)): ?>
    <p>Ingen aktive spillere</p>
<?php else: ?>
<table>
    <thead>
        <tr>
            <th>Handlinger</th>
            <th>Navn</th>
            <th>Skill</th>
            <th>Runner</th>
            <th>Køn</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($activePlayers as $p): ?>
            <tr>
                <td class="actions">
                    <a class="btn btn-edit"
                       href="player_form.php?id=<?= $p['id'] ?>">Rediger</a>
                    <a class="btn btn-toggle"
                       href="toggle_active.php?id=<?= $p['id'] ?>&to=0"
                       onclick="return confirm('Deaktiver denne spiller?')">
                       Deaktiver
                    </a>
                </td>
                <td class="name"><?= htmlspecialchars($p['name']) ?></td>
                <td class="skill"><?= $p['skill_level'] ?></td>
                <td class="runner"><?= $p['is_runner'] ? 'Ja' : 'Nej' ?></td>
                <td class="gender"><?= ucfirst($p['gender']) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>

<details>
    <summary><strong>Inaktive spillere</strong></summary>

    <?php if (empty($inactivePlayers)): ?>
        <p>Ingen inaktive spillere</p>
    <?php else: ?>
    <table class="inactive">
        <thead>
            <tr>
                <th>Handlinger</th>
                <th>Navn</th>
                <th>Skill</th>
                <th>Runner</th>
                <th>Køn</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($inactivePlayers as $p): ?>
                <tr>
                    <td class="actions">
                        <a class="btn btn-toggle"
                           href="toggle_active.php?id=<?= $p['id'] ?>&to=1"
                           onclick="return confirm('Genaktivér denne spiller?')">
                           Genaktivér
                        </a>
                    </td>
                    <td class="name"><?= htmlspecialchars($p['name']) ?></td>
                    <td class="skill"><?= $p['skill_level'] ?></td>
                    <td class="runner"><?= $p['is_runner'] ? 'Ja' : 'Nej' ?></td>
                    <td class="gender"><?= ucfirst($p['gender']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</details>

</body>
</html>
