<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=adamson_ccit', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Creating student_profiles table...\n";
    
    $sql = "
    CREATE TABLE IF NOT EXISTS student_profiles (
        id INT PRIMARY KEY AUTO_INCREMENT,
        user_id INT NOT NULL,
        student_id VARCHAR(20),
        first_name VARCHAR(100),
        last_name VARCHAR(100),
        middle_name VARCHAR(100),
        email VARCHAR(255),
        phone VARCHAR(20),
        address TEXT,
        birth_date DATE,
        program VARCHAR(100),
        year_level ENUM('1st Year', '2nd Year', '3rd Year', '4th Year', 'Graduate'),
        section VARCHAR(10),
        gpa DECIMAL(3,2),
        bio TEXT,
        skills TEXT,
        interests TEXT,
        linkedin_url VARCHAR(255),
        github_url VARCHAR(255),
        portfolio_url VARCHAR(255),
        profile_image VARCHAR(255),
        status ENUM('active', 'inactive', 'graduated', 'dropped') DEFAULT 'active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        UNIQUE KEY unique_user (user_id)
    )";
    
    $pdo->exec($sql);
    echo "✓ student_profiles table created successfully!\n";
    
    // Insert sample data for existing users
    echo "\nInserting sample profile data...\n";
    
    $stmt = $pdo->query("SELECT id, username FROM users WHERE role = 'student' LIMIT 5");
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $samplePrograms = ['BS Computer Science', 'BS Information Technology', 'BS Computer Engineering', 'BS Data Science'];
    $sampleYears = ['1st Year', '2nd Year', '3rd Year', '4th Year'];
    
    foreach ($students as $index => $student) {
        // Check if profile already exists
        $checkStmt = $pdo->prepare("SELECT id FROM student_profiles WHERE user_id = ?");
        $checkStmt->execute([$student['id']]);
        
        if (!$checkStmt->fetch()) {
            $insertStmt = $pdo->prepare("
                INSERT INTO student_profiles 
                (user_id, student_id, first_name, last_name, email, program, year_level, bio, skills) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $firstName = ucfirst($student['username']);
            $lastName = 'Santos';
            $studentId = '202' . str_pad($student['id'], 4, '0', STR_PAD_LEFT);
            $email = strtolower($student['username']) . '@student.adamson.edu.ph';
            $program = $samplePrograms[$index % count($samplePrograms)];
            $year = $sampleYears[$index % count($sampleYears)];
            $bio = "Passionate " . $program . " student at Adamson University, focused on building technical expertise and professional certifications in the technology field.";
            $skills = "Programming, Web Development, Database Management, Problem Solving";
            
            $insertStmt->execute([
                $student['id'], $studentId, $firstName, $lastName, $email, $program, $year, $bio, $skills
            ]);
            
            echo "✓ Created profile for {$student['username']}\n";
        } else {
            echo "- Profile already exists for {$student['username']}\n";
        }
    }
    
    echo "\n✓ Sample data inserted successfully!\n";
    
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . "\n";
}
?>