<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'auth.php';
require_once __DIR__ . '/../config/db.php';

$errors = [];
$id = null;

/* Defaults (CREATE) */
$name = '';
$skill = 3;
$is_runner = 0;
$gender = 'male';

/* EDIT MODE */
if (isset($_GET['id']) && ctype_digit($_GET['id'])) {
    $id = (int)$_GET['id'];

    $stmt = $pdo->prepare(
        "SELECT * FROM floorball_players WHERE id = ?"
    );
    $stmt->execute([$id]);
    $player = $stmt->fetch();

    if ($player) {
        $name = $player['name'];
        $skill = $player['skill_level'];
        $is_runner = $player['is_runner'];
        $gender = $player['gender'];
    }
}

/* FORM SUBMIT */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $skill = (int)($_POST['skill_level'] ?? 0);
    $is_runner = isset($_POST['is_runner']) ? 1 : 0;
    $gender = $_POST['gender'] ?? '';

    if ($name === '') {
        $errors[] = 'Name is required';
    }

    if ($skill < 1 || $skill > 5) {
        $errors[] = 'Skill level must be between 1 and 5';
    }

    if (!in_array($gender, ['male', 'female'], true)) {
        $errors[] = 'Gender is required';
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare(
            "SELECT COUNT(*) FROM floorball_players WHERE name = ? AND id != ?"
        );
        $stmt->execute([$name, $id ?? 0]);

        if ($stmt->fetchColumn() > 0) {
            $errors[] = 'A player with this name already exists';
        }
    }

    if (empty($errors)) {
        if ($id) {
            $stmt = $pdo->prepare(
                "UPDATE floorball_players
                 SET name = ?, skill_level = ?, is_runner = ?, gender = ?
                 WHERE id = ?"
            );
            $stmt->execute([$name, $skill, $is_runner, $gender, $id]);
        } else {
            $stmt = $pdo->prepare(
                "INSERT INTO floorball_players
                 (name, skill_level, is_runner, gender)
                 VALUES (?, ?, ?, ?)"
            );
            $stmt->execute([$name, $skill, $is_runner, $gender]);
        }

        header('Location: index.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="da">
<head>
    <meta charset="UTF-8">
    <title><?= $id ? 'Rediger spiller' : 'Ny spiller' ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../css/app.css">
</head>
<body>

<div class="admin-top">
    <a class="btn btn-toggle" href="index.php">← Tilbage</a>
</div>

<h1><?= $id ? 'Rediger spiller' : 'Opret ny spiller' ?></h1>

<form method="post" class="player-form">

    <label>
        Navn
        <input
            type="text"
            name="name"
            value="<?= htmlspecialchars($name) ?>"
            required
        >
    </label>

    <label>
        Skill-niveau
        <span class="range-value"><strong><?= $skill ?></strong></span>
        <input
            type="range"
            name="skill_level"
            min="1"
            max="5"
            step="1"
            value="<?= $skill ?>"
            oninput="this.previousElementSibling.querySelector('strong').textContent = this.value"
        >
    </label>

    <label class="checkbox">
        <input type="checkbox" name="is_runner" <?= $is_runner ? 'checked' : '' ?>>
        Runner
    </label>

    <fieldset>
        <legend>Køn</legend>

        <label class="radio">
            <input type="radio" name="gender" value="male" <?= $gender === 'male' ? 'checked' : '' ?>>
            Mand
        </label>

        <label class="radio">
            <input type="radio" name="gender" value="female" <?= $gender === 'female' ? 'checked' : '' ?>>
            Kvinde
        </label>
    </fieldset>

    <button type="submit">
        <?= $id ? 'Gem ændringer' : 'Opret spiller' ?>
    </button>

</form>

</body>
</html>

