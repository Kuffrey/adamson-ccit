<?php
// Create Faculty Certification Tables
require_once __DIR__ . '/app/config/Database.php';

try {
    $db = Database::getInstance()->getConnection();
    
    echo "<h1>Creating Faculty Certification Tables</h1>";
    
    // Create certification table
    $sql1 = "
    CREATE TABLE IF NOT EXISTS certification (
        id INT AUTO_INCREMENT PRIMARY KEY,
        cert_title VARCHAR(255) NOT NULL,
        issuer VARCHAR(255) NOT NULL,
        issuer_key VARCHAR(100),
        badge_url VARCHAR(500),
        cert_url VARCHAR(500),
        verify_url VARCHAR(500),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    
    $db->exec($sql1);
    echo "✅ Created/verified 'certification' table<br>";
    
    // Create faculty table if not exists
    $sql2 = "
    CREATE TABLE IF NOT EXISTS faculty (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        dept VARCHAR(100),
        position VARCHAR(100),
        email VARCHAR(255),
        status ENUM('active', 'inactive') DEFAULT 'active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    
    $db->exec($sql2);
    echo "✅ Created/verified 'faculty' table<br>";
    
    // Create faculty_certification_award table
    $sql3 = "
    CREATE TABLE IF NOT EXISTS faculty_certification_award (
        id INT AUTO_INCREMENT PRIMARY KEY,
        faculty_id INT NOT NULL,
        certification_id INT NOT NULL,
        year_earned VARCHAR(4) NOT NULL,
        year_expiry VARCHAR(4),
        status ENUM('Active', 'Expired', 'Revoked') DEFAULT 'Active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (faculty_id) REFERENCES faculty(id) ON DELETE CASCADE,
        FOREIGN KEY (certification_id) REFERENCES certification(id) ON DELETE CASCADE
    )";
    
    $db->exec($sql3);
    echo "✅ Created/verified 'faculty_certification_award' table<br>";
    
    // Create settings table
    $sql4 = "
    CREATE TABLE IF NOT EXISTS faculty_certifications_page_settings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        subhero_image_url VARCHAR(500),
        subhero_lead TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    
    $db->exec($sql4);
    echo "✅ Created/verified 'faculty_certifications_page_settings' table<br>";
    
    // Insert default settings if empty
    $stmt = $db->prepare("SELECT COUNT(*) FROM faculty_certifications_page_settings");
    $stmt->execute();
    if ($stmt->fetchColumn() == 0) {
        $stmt = $db->prepare("INSERT INTO faculty_certifications_page_settings (subhero_image_url, subhero_lead) VALUES (?, ?)");
        $stmt->execute([
            '/adamson-ccit/public/assets/images/hero-campus.jpg',
            'Professional badges, licenses, and industry certifications held by CCIT faculty.'
        ]);
        echo "✅ Inserted default settings<br>";
    }
    
    // Insert sample data if tables are empty
    
    // Sample certifications
    $stmt = $db->prepare("SELECT COUNT(*) FROM certification");
    $stmt->execute();
    if ($stmt->fetchColumn() == 0) {
        $certifications = [
            ['Microsoft Certified: Azure Fundamentals', 'Microsoft', 'azure-fundamentals'],
            ['AWS Certified Solutions Architect', 'Amazon Web Services', 'aws-solutions-architect'],
            ['Google Cloud Professional Data Engineer', 'Google Cloud', 'gcp-data-engineer'],
            ['Cisco Certified Network Associate (CCNA)', 'Cisco', 'ccna'],
            ['CompTIA Security+', 'CompTIA', 'security-plus']
        ];
        
        foreach ($certifications as $cert) {
            $stmt = $db->prepare("INSERT INTO certification (cert_title, issuer, issuer_key) VALUES (?, ?, ?)");
            $stmt->execute($cert);
        }
        echo "✅ Inserted sample certifications<br>";
    }
    
    // Sample faculty
    $stmt = $db->prepare("SELECT COUNT(*) FROM faculty");
    $stmt->execute();
    if ($stmt->fetchColumn() == 0) {
        $faculty = [
            ['Dr. John Smith', 'Computer Science', 'Professor'],
            ['Dr. Jane Doe', 'Information Technology', 'Associate Professor'],
            ['Prof. Mike Johnson', 'Software Engineering', 'Assistant Professor'],
            ['Dr. Sarah Wilson', 'Cybersecurity', 'Professor'],
            ['Prof. David Brown', 'Data Science', 'Associate Professor']
        ];
        
        foreach ($faculty as $fac) {
            $stmt = $db->prepare("INSERT INTO faculty (name, dept, position) VALUES (?, ?, ?)");
            $stmt->execute($fac);
        }
        echo "✅ Inserted sample faculty<br>";
    }
    
    echo "<h3>✅ All tables created successfully!</h3>";
    echo "<p><a href='test_cert_db.php'>Test Database Structure</a></p>";
    echo "<p><a href='admin/faculty_certifications'>Go to Faculty Certifications Admin</a></p>";
    
} catch (Exception $e) {
    echo "<h3 style='color: red;'>Error:</h3>";
    echo "<p>" . $e->getMessage() . "</p>";
}
?>