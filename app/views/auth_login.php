<?php
// public/auth_login.php
declare(strict_types=1);

// Always start session BEFORE any output
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

// ---- Demo credentials (replace with DB lookup) ----
$users = [
  'student' => ['password' => 'student123', 'role' => 'student'],
  'faculty' => ['password' => 'faculty123', 'role' => 'faculty'],
];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: /adamson-ccit/public/index.php?page=login_guest_student');
  exit;
}

$username = isset($_POST['username']) ? trim((string)$_POST['username']) : '';
$password = (string)($_POST['password'] ?? '');

if ($username !== '' && isset($users[$username]) && hash_equals($users[$username]['password'], $password)) {
  $_SESSION['user'] = [
    'username' => $username,
    'role'     => $users[$username]['role'],
  ];

  // Role-based landing
  if ($users[$username]['role'] === 'student') {
    header('Location: /adamson-ccit/public/index.php?page=dashboard_student');
  } else {
    header('Location: /adamson-ccit/public/index.php?page=dashboard_faculty');
  }
  exit;
}

// Failed login
header('Location: /adamson-ccit/public/index.php?page=login_guest_student&error=1');
exit;
