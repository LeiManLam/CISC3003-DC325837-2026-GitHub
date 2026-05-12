<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

require_once dirname(__DIR__) . '/connect.php';

$emailRaw = $_GET['email'] ?? $_POST['email'] ?? '';
$email = filter_var(trim((string)$emailRaw), FILTER_VALIDATE_EMAIL);

if ($email === false) {
    echo json_encode(['ok' => false, 'error' => 'invalid_email']);
    exit;
}

$stmt = $mysqli->prepare('SELECT COUNT(*) AS c FROM users WHERE email = ?');
if (!$stmt) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'server']);
    exit;
}
$stmt->bind_param('s', $email);
$stmt->execute();
$res = $stmt->get_result()->fetch_assoc();
$stmt->close();

$count = (int)($res['c'] ?? 0);
echo json_encode(['ok' => true, 'available' => $count === 0]);
