<?php
// Check graduate programs database structure
require_once 'app/config/database.php';

echo "<h1>Graduate Programs Database Analysis</h1>";

try {
    $db = new PDO("mysql:host=localhost;dbname=adamson_ccit", "root", "");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check what tables exist for graduate programs
    echo "<h2>Available Tables Related to Graduate Programs:</h2>";
    $stmt = $db->query("SHOW TABLES LIKE '%graduate%'");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (empty($tables)) {
        echo "<p>No graduate-specific tables found. Checking for programs_graduate...</p>";
        $stmt = $db->query("SHOW TABLES LIKE 'programs_graduate'");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
    
    if (empty($tables)) {
        echo "<p style='color: orange;'>No programs_graduate table found. Will need to create it.</p>";
        
        // Create the graduate programs table
        echo "<h2>Creating programs_graduate table...</h2>";
        $createSql = "CREATE TABLE IF NOT EXISTS programs_graduate (
            id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
            is_active TINYINT(1) DEFAULT 1,
            position INT(11) DEFAULT 1,
            slug VARCHAR(255) UNIQUE,
            badge VARCHAR(50),
            title VARCHAR(255) NOT NULL,
            title_muted VARCHAR(255),
            summary TEXT,
            pillbox_title VARCHAR(255),
            pills TEXT,
            learn_more_url VARCHAR(500),
            learn_more_external TINYINT(1) DEFAULT 0,
            curriculum_url VARCHAR(500),
            curriculum_external TINYINT(1) DEFAULT 0,
            apply_url VARCHAR(500),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )";
        
        $db->exec($createSql);
        echo "<span style='color: green;'>✓ Created programs_graduate table!</span><br>";
        
        // Add sample graduate programs
        echo "<h2>Adding sample graduate programs...</h2>";
        $programs = [
            [
                'badge' => 'MIT',
                'title' => 'Master of Science in Information Technology',
                'title_muted' => '(Advanced Computing)',
                'slug' => 'mit',
                'summary' => 'Advanced graduate program focusing on cutting-edge IT research, enterprise systems, and emerging technologies in the digital landscape.',
                'pillbox_title' => 'MIT Specializations',
                'pills' => "Advanced Software Engineering\nData Analytics & AI\nCybersecurity\nCloud Computing\nDigital Transformation",
                'learn_more_url' => '/adamson-ccit/public/index.php?page=programs_graduate&program=mit',
                'curriculum_url' => '/adamson-ccit/public/assets/documents/curriculum-mit.pdf',
                'apply_url' => '/adamson-ccit/public/index.php?page=admission_graduate',
                'position' => 1,
                'is_active' => 1
            ],
            [
                'badge' => 'PhD CS',
                'title' => 'Doctor of Philosophy in Computer Science',
                'title_muted' => '(Research & Innovation)',
                'slug' => 'phd-cs',
                'summary' => 'Premier doctoral program for advanced research in computer science, preparing scholars and researchers for academia and industry leadership.',
                'pillbox_title' => 'PhD Research Areas',
                'pills' => "Artificial Intelligence\nMachine Learning\nHuman-Computer Interaction\nSoftware Engineering\nTheoretical Computer Science",
                'learn_more_url' => '/adamson-ccit/public/index.php?page=programs_graduate&program=phd-cs',
                'curriculum_url' => '/adamson-ccit/public/assets/documents/curriculum-phd-cs.pdf',
                'apply_url' => '/adamson-ccit/public/index.php?page=admission_graduate',
                'position' => 2,
                'is_active' => 1
            ],
            [
                'badge' => 'MSCS',
                'title' => 'Master of Science in Computer Science',
                'title_muted' => '(Thesis & Non-Thesis)',
                'slug' => 'mscs',
                'summary' => 'Comprehensive graduate program in computer science with thesis and non-thesis tracks, focusing on advanced algorithms, software development, and research methodology.',
                'pillbox_title' => 'MSCS Tracks',
                'pills' => "Thesis Track\nNon-Thesis Track\nSoftware Systems\nData Science\nComputer Networks",
                'learn_more_url' => '/adamson-ccit/public/index.php?page=programs_graduate&program=mscs',
                'curriculum_url' => '/adamson-ccit/public/assets/documents/curriculum-mscs.pdf',
                'apply_url' => '/adamson-ccit/public/index.php?page=admission_graduate',
                'position' => 3,
                'is_active' => 1
            ]
        ];
        
        foreach ($programs as $program) {
            $fields = array_keys($program);
            $placeholders = array_fill(0, count($fields), '?');
            
            $insertSql = "INSERT INTO programs_graduate (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $placeholders) . ")";
            $stmt = $db->prepare($insertSql);
            $stmt->execute(array_values($program));
            
            echo "<span style='color: green;'>✓ Added {$program['badge']}</span><br>";
        }
        
    } else {
        foreach ($tables as $table) {
            echo "<h2>Table: $table</h2>";
            
            // Show structure
            $stmt = $db->query("DESCRIBE $table");
            $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo "<h3>Structure:</h3>";
            echo "<table border='1' style='border-collapse: collapse;'>";
            echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
            foreach ($columns as $col) {
                echo "<tr>";
                echo "<td>" . $col['Field'] . "</td>";
                echo "<td>" . $col['Type'] . "</td>";
                echo "<td>" . $col['Null'] . "</td>";
                echo "<td>" . $col['Key'] . "</td>";
                echo "<td>" . ($col['Default'] ?? 'NULL') . "</td>";
                echo "</tr>";
            }
            echo "</table>";
            
            // Show data
            $stmt = $db->query("SELECT COUNT(*) as count FROM $table");
            $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
            echo "<h3>Data: $count rows</h3>";
            
            if ($count > 0) {
                $stmt = $db->query("SELECT * FROM $table LIMIT 5");
                $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                if (!empty($data)) {
                    echo "<table border='1' style='border-collapse: collapse;'>";
                    echo "<tr>";
                    foreach (array_keys($data[0]) as $header) {
                        echo "<th>" . $header . "</th>";
                    }
                    echo "</tr>";
                    
                    foreach ($data as $row) {
                        echo "<tr>";
                        foreach ($row as $value) {
                            echo "<td>" . ($value ?? 'NULL') . "</td>";
                        }
                        echo "</tr>";
                    }
                    echo "</table>";
                }
            }
        }
    }
    
} catch (Exception $e) {
    echo "<h2 style='color: red;'>Error:</h2>";
    echo $e->getMessage();
}
?>