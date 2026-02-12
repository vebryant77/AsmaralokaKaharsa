<?php
// api/login.php
// Endpoint untuk login user

session_start();
require_once __DIR__ . '/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  json_response(['error' => 'Method not allowed'], 405);
}

$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;

$username = trim($input['username'] ?? '');
$password = $input['password'] ?? '';

if (!$username || !$password) {
  json_response(['error' => 'Username dan password wajib diisi'], 400);
}

// Cari user berdasarkan username
$stmt = $pdo->prepare('SELECT id, username, password, nama_depan, nama_belakang, email, no_telp FROM user WHERE username = ? LIMIT 1');
$stmt->execute([$username]);
$user = $stmt->fetch();

if (!$user || !password_verify($password, $user['password'])) {
  json_response(['error' => 'Username atau password salah'], 401);
}

// Simpan session
$_SESSION['user_id'] = $user['id'];
$_SESSION['username'] = $user['username'];

// Hapus password dari response
unset($user['password']);

json_response([
  'success' => true,
  'message' => 'Login berhasil',
  'user' => $user
]);
?>
