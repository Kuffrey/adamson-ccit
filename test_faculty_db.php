<?php
// Test database connectivity for Faculty CMS
require_once 'app/config/Database.php';
require_once 'app/models/FacultyProfile.php';
require_once 'app/models/FacultyResearch.php';
require_once 'app/models/FacultyCertificationAward.php';

try {
    echo "<h1>Faculty CMS Database Connectivity Test</h1>";
    echo "<p>Testing connection to adamson_ccit database...</p>";
    
    // Test database connection
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    echo "✓ Database connection successful<br><br>";
    
    // Test Faculty Profile table
    echo "<h3>Faculty Profile Table Test:</h3>";
    $faculty = FacultyProfile::getAll();
    echo "✓ Faculty profiles found: " . count($faculty) . "<br>";
    foreach ($faculty as $member) {
        echo "- " . $member['first_name'] . " " . $member['last_name'] . " (" . $member['status'] . ")<br>";
    }
    echo "<br>";
    
    // Test Faculty Research table
    echo "<h3>Faculty Research Table Test:</h3>";
    $research = FacultyResearch::getAll();
    echo "✓ Research entries found: " . count($research) . "<br>";
    foreach ($research as $entry) {
        echo "- " . $entry['title'] . " (" . $entry['research_type'] . ")<br>";
    }
    echo "<br>";
    
    // Test Faculty Certification Award table
    echo "<h3>Faculty Certification Award Table Test:</h3>";
    $certifications = FacultyCertificationAward::getAll();
    echo "✓ Certification entries found: " . count($certifications) . "<br>";
    foreach ($certifications as $cert) {
        echo "- " . $cert['faculty_name'] . ": " . $cert['certification_name'] . " (" . $cert['status'] . ")<br>";
    }
    echo "<br>";
    
    echo "<h3>✅ All database connections are working properly!</h3>";
    echo "<p>The Faculty CMS system is fully connected to the adamson_ccit database.</p>";
    
} catch (Exception $e) {
    echo "<h3>❌ Database Error:</h3>";
    echo "<p style='color: red;'>" . $e->getMessage() . "</p>";
}
?>