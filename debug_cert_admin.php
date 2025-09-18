<?php
// Debug Faculty Certifications Admin
if (session_status() === PHP_SESSION_NONE) session_start();

// Bypass authentication for testing
$_SESSION['user'] = ['username' => 'Admin', 'role' => 'admin'];

require_once __DIR__ . '/app/config/Database.php';
require_once __DIR__ . '/app/models/Model.php';
require_once __DIR__ . '/app/models/FacultyCertification.php';
require_once __DIR__ . '/app/models/FacultyCertificationsPageSettings.php';

try {
    echo "<h1>Faculty Certifications Debug Test</h1>";
    
    // Test database connection
    $db = Database::getInstance()->getConnection();
    echo "✅ Database connection successful<br><br>";
    
    // Test models
    echo "<h3>Testing Models:</h3>";
    
    $facultyCertification = new FacultyCertification();
    echo "✅ FacultyCertification model created<br>";
    
    $pageSettings = new FacultyCertificationsPageSettings();
    echo "✅ FacultyCertificationsPageSettings model created<br>";
    
    // Test data retrieval
    echo "<h3>Testing Data Retrieval:</h3>";
    
    $settings = $pageSettings->getSettings();
    echo "✅ Settings retrieved: " . count($settings) . " items<br>";
    
    $certifications = $facultyCertification->getAll();
    echo "✅ Certifications retrieved: " . count($certifications) . " items<br>";
    
    $availableCertifications = FacultyCertification::getAllCertifications();
    echo "✅ Available certifications retrieved: " . count($availableCertifications) . " items<br>";
    
    $facultyList = FacultyCertification::getAllFaculty();
    echo "✅ Faculty list retrieved: " . count($facultyList) . " items<br>";
    
    // Test form submission if POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        echo "<h3>Testing Form Submission:</h3>";
        echo "<pre>";
        print_r($_POST);
        echo "</pre>";
        
        if (isset($_POST['action'])) {
            switch ($_POST['action']) {
                case 'add_certification':
                    try {
                        $result = $facultyCertification->create($_POST);
                        echo "✅ Certification added successfully<br>";
                    } catch (Exception $e) {
                        echo "❌ Error adding certification: " . $e->getMessage() . "<br>";
                    }
                    break;
                    
                case 'delete_certification':
                    try {
                        $result = $facultyCertification->delete($_POST['id']);
                        echo "✅ Certification deleted successfully<br>";
                    } catch (Exception $e) {
                        echo "❌ Error deleting certification: " . $e->getMessage() . "<br>";
                    }
                    break;
            }
        }
    }
    
    echo "<hr>";
    
    // Show simple forms for testing
    echo "<h3>Add Certification Test Form:</h3>";
    echo "<form method='POST'>";
    echo "<input type='hidden' name='action' value='add_certification'>";
    echo "<label>Faculty:</label><br>";
    echo "<select name='faculty_id' required>";
    foreach ($facultyList as $faculty) {
        echo "<option value='{$faculty['id']}'>{$faculty['name']} ({$faculty['dept']})</option>";
    }
    echo "</select><br><br>";
    
    echo "<label>Certification:</label><br>";
    echo "<select name='certification_id' required>";
    foreach ($availableCertifications as $cert) {
        echo "<option value='{$cert['id']}'>{$cert['cert_title']} ({$cert['issuer']})</option>";
    }
    echo "</select><br><br>";
    
    echo "<label>Year Earned:</label><br>";
    echo "<input type='text' name='year_earned' value='2024' required><br><br>";
    
    echo "<label>Status:</label><br>";
    echo "<select name='status' required>";
    echo "<option value='Active'>Active</option>";
    echo "<option value='Expired'>Expired</option>";
    echo "<option value='Revoked'>Revoked</option>";
    echo "</select><br><br>";
    
    echo "<button type='submit'>Add Certification</button>";
    echo "</form>";
    
    echo "<h3>Current Certifications:</h3>";
    if (empty($certifications)) {
        echo "<p>No certifications found.</p>";
    } else {
        echo "<table border='1'>";
        echo "<tr><th>ID</th><th>Faculty</th><th>Certification</th><th>Year</th><th>Status</th><th>Actions</th></tr>";
        foreach ($certifications as $cert) {
            echo "<tr>";
            echo "<td>{$cert['id']}</td>";
            echo "<td>" . htmlspecialchars($cert['faculty_name'] ?? 'Unknown') . "</td>";
            echo "<td>" . htmlspecialchars($cert['cert_title'] ?? 'Unknown') . "</td>";
            echo "<td>{$cert['year_earned']}</td>";
            echo "<td>{$cert['status']}</td>";
            echo "<td>";
            echo "<form method='POST' style='display:inline;'>";
            echo "<input type='hidden' name='action' value='delete_certification'>";
            echo "<input type='hidden' name='id' value='{$cert['id']}'>";
            echo "<button type='submit' onclick='return confirm(\"Delete?\")'>Delete</button>";
            echo "</form>";
            echo "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
} catch (Exception $e) {
    echo "<h3 style='color: red;'>Error:</h3>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>