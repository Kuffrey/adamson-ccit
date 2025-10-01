<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=adamson_ccit', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Creating faculty_portfolio table...\n";
    
    $sql = "
    CREATE TABLE IF NOT EXISTS faculty_portfolio (
        id INT PRIMARY KEY AUTO_INCREMENT,
        user_id INT NOT NULL,
        name VARCHAR(255) NOT NULL,
        company_id INT NOT NULL,
        issue_month TINYINT NULL,
        issue_year SMALLINT NULL,
        expires TINYINT(1) DEFAULT 1 COMMENT '0=no expiry, 1=has expiry',
        expire_month TINYINT NULL,
        expire_year SMALLINT NULL,
        credential_id VARCHAR(100) NULL,
        credential_url VARCHAR(500) NULL,
        visibility ENUM('public', 'private') DEFAULT 'public',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_user_id (user_id),
        INDEX idx_company_id (company_id),
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE RESTRICT
    )";
    
    $pdo->exec($sql);
    echo "✓ faculty_portfolio table created successfully!\n";
    
    // Insert sample data for existing faculty users
    echo "\nInserting sample portfolio data...\n";
    
    $stmt = $pdo->query("SELECT id, username FROM users WHERE role = 'faculty' LIMIT 3");
    $faculty = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get some companies for reference
    $companyStmt = $pdo->query("SELECT id FROM companies LIMIT 5");
    $companies = $companyStmt->fetchAll(PDO::FETCH_COLUMN);
    
    $sampleCertifications = [
        'AWS Certified Solutions Architect',
        'Microsoft Azure Fundamentals',
        'Google Cloud Professional',
        'Cisco Certified Network Associate',
        'CompTIA Security+',
        'Project Management Professional (PMP)',
        'Certified Information Systems Security Professional (CISSP)',
        'Oracle Certified Professional',
        'VMware Certified Professional',
        'Red Hat Certified Engineer'
    ];
    
    if (!empty($companies)) {
        foreach ($faculty as $index => $facultyMember) {
            // Check if portfolio already exists
            $checkStmt = $pdo->prepare("SELECT id FROM faculty_portfolio WHERE user_id = ?");
            $checkStmt->execute([$facultyMember['id']]);
            
            if (!$checkStmt->fetch()) {
                // Add 2-3 certifications per faculty
                $numCerts = rand(2, 3);
                $usedCerts = [];
                
                for ($i = 0; $i < $numCerts; $i++) {
                    do {
                        $certIndex = array_rand($sampleCertifications);
                        $certName = $sampleCertifications[$certIndex];
                    } while (in_array($certName, $usedCerts));
                    
                    $usedCerts[] = $certName;
                    
                    $insertStmt = $pdo->prepare("
                        INSERT INTO faculty_portfolio 
                        (user_id, name, company_id, issue_month, issue_year, expires, expire_month, expire_year, credential_id, visibility) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                    ");
                    
                    $companyId = $companies[array_rand($companies)];
                    $issueMonth = rand(1, 12);
                    $issueYear = rand(2020, 2024);
                    $expires = rand(0, 1); // Random expiry
                    $expireMonth = $expires ? rand(1, 12) : null;
                    $expireYear = $expires ? rand(2025, 2027) : null;
                    $credentialId = 'CERT-' . strtoupper(substr(md5($certName . $facultyMember['id']), 0, 8));
                    $visibility = rand(0, 1) ? 'public' : 'private';
                    
                    $insertStmt->execute([
                        $facultyMember['id'], $certName, $companyId, $issueMonth, $issueYear, 
                        $expires, $expireMonth, $expireYear, $credentialId, $visibility
                    ]);
                    
                    echo "✓ Added certification '{$certName}' for {$facultyMember['username']}\n";
                }
            } else {
                echo "- Portfolio already exists for {$facultyMember['username']}\n";
            }
        }
    }
    
    echo "\n✓ Faculty portfolio setup completed successfully!\n";
    
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . "\n";
}
?>