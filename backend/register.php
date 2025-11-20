<?php
require_once __DIR__ . '/db.php';

// Allow CORS for local development (adjust in production)
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
$firstName = isset($data['firstName']) ? trim($data['firstName']) : '';
$middleName = isset($data['middleName']) ? trim($data['middleName']) : '';
$lastName = isset($data['lastName']) ? trim($data['lastName']) : '';
$country = isset($data['country']) ? trim(strtolower($data['country'])) : '';

if (!$email || !$password) {
    jsonResponse(['success' => false, 'error' => 'Email and password are required'], 400);
}

try {
    $pdo = getPDO();

    // check duplicate
    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = :email LIMIT 1');
    $stmt->execute([':email' => $email]);
    if ($stmt->fetch()) {
        jsonResponse(['success' => false, 'error' => 'Email already registered'], 409);
    }

    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    $insert = $pdo->prepare('INSERT INTO users (email, password_hash, first_name, middle_name, last_name, country, created_at) VALUES (:email, :password_hash, :first_name, :middle_name, :last_name, :country, NOW())');
    $insert->execute([
        ':email' => $email,
        ':password_hash' => $passwordHash,
        ':first_name' => $firstName,
        ':middle_name' => $middleName,
        ':last_name' => $lastName,
        ':country' => $country
    ]);

    $userId = (int)$pdo->lastInsertId();
    jsonResponse([
        'success' => true,
        'message' => 'Registered successfully',
        'user' => [
            'id' => $userId,
            'email' => $email,
            'first_name' => $firstName,
            'middle_name' => $middleName,
            'last_name' => $lastName,
            'country' => $country
        ]
    ]);
} catch (Exception $ex) {
    jsonResponse(['success' => false, 'error' => 'Server error', 'detail' => $ex->getMessage()], 500);
}

?>
