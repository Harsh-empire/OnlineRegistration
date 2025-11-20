<?php
require_once __DIR__ . '/db.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$data = readJsonBody();
if (!$data) {
    jsonResponse(['success' => false, 'error' => 'Invalid JSON body'], 400);
}

$email = isset($data['email']) ? trim(strtolower($data['email'])) : '';
$password = isset($data['password']) ? $data['password'] : '';

if (!$email || !$password) {
    jsonResponse(['success' => false, 'error' => 'Email and password required'], 400);
}

try {
    $pdo = getPDO();
    $stmt = $pdo->prepare('SELECT id, email, password_hash, first_name, middle_name, last_name, country FROM users WHERE email = :email LIMIT 1');
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch();
    if (!$user) {
        jsonResponse(['success' => false, 'error' => 'Invalid credentials'], 401);
    }

    if (!password_verify($password, $user['password_hash'])) {
        jsonResponse(['success' => false, 'error' => 'Invalid credentials'], 401);
    }

    // Return some profile info (don't send hash)
    unset($user['password_hash']);
    jsonResponse(['success' => true, 'user' => $user]);
} catch (Exception $ex) {
    jsonResponse(['success' => false, 'error' => 'Server error', 'detail' => $ex->getMessage()], 500);
}

?>
