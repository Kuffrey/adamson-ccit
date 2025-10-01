<?php
require_once __DIR__ . '/app/models/Model.php';
require_once __DIR__ . '/app/models/FacultyCertification.php';

try {
    echo "Available Certifications with IDs:" . PHP_EOL;
    
    $certifications = FacultyCertification::getAllCertifications();
    
    if (is_array($certifications)) {
        foreach ($certifications as $cert) {
            echo "ID: " . $cert['id'] . " | Title: " . $cert['cert_title'] . " | Issuer: " . $cert['issuer'] . PHP_EOL;
        }
        
        echo PHP_EOL . "Total certifications: " . count($certifications) . PHP_EOL;
        echo "IDs available: " . implode(', ', array_column($certifications, 'id')) . PHP_EOL;
    } else {
        echo "ERROR: No certifications returned or not an array" . PHP_EOL;
    }
    
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . PHP_EOL;
}
?>