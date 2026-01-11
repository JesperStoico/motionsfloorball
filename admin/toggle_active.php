<?php
require_once 'auth.php';
require_once __DIR__ . '/../config/db.php';

$id = (int)($_GET['id'] ?? 0);
$to = isset($_GET['to']) ? (int)$_GET['to'] : null;

if (!$id || !in_array($to, [0, 1], true)) {
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare("
    UPDATE floorball_players
    SET is_active = ?
    WHERE id = ?
");
$stmt->execute([$to, $id]);

header('Location: index.php');
exit;
