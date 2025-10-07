<?php
// app/models/DeanCorner.php
class DeanCorner {
    public static function get(PDO $pdo): array {
        $row = $pdo->query("SELECT * FROM deans_corner ORDER BY updated_at DESC LIMIT 1")->fetch();
        return $row ?: [
            'name' => 'Dr. John Doe',
            'title' => 'Dean, College of Computing and Information Technology',
            'photo' => '/adamson-ccit/public/assets/images/dean-john-doe.jpg',
            'message' => 'Welcome to the College of Computing and Information Technology at Adamson University! Our commitment is to provide world-class education, foster innovation, and prepare our students for successful careers in IT and computing. We invite you to explore our programs, research, and vibrant community.'
        ];
    }
}
