<?php
// StudentLoginController.php
require_once __DIR__ . '/../lib/Auth.php';

class StudentLoginController {
    public static function handle() {
        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = trim($_POST['password'] ?? '');

            // Demo only — replace with DB lookup later
            if ($username === 'student' && $password === 'student123') {
                Auth::login(['username' => 'student', 'role' => 'student']);
                header('Location: /adamson-ccit/public/index.php');
                exit;
            } else {
                $error = 'Invalid credentials';
            }
        }
        return $error;
    }
}