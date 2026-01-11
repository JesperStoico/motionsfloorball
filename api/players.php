<?php
require_once __DIR__ . '/../config/db.php';

header('Content-Type: application/json; charset=utf-8');

$stmt = $pdo->prepare("
    SELECT id, name, skill_level, is_runner, gender
    FROM floorball_players
    WHERE is_active = 1
    ORDER BY name
");
$stmt->execute();

$players = $stmt->fetchAll();

// Cast runner to boolean for clean JSON
foreach ($players as &$p) {
    $p['is_runner'] = (bool)$p['is_runner'];
}

echo json_encode($players);
