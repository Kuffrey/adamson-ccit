<?php
// Quick verification script for faculty admin pages
echo "<h2>Faculty Admin Pages CSS Verification</h2>";

$pages = [
    'Profile' => '/app/views/admin/admin_faculty_profile.php',
    'Research' => '/app/views/admin/admin_faculty_research.php', 
    'Certifications' => '/app/views/admin/admin_faculty_certifications.php'
];

foreach ($pages as $name => $path) {
    $fullPath = __DIR__ . $path;
    if (file_exists($fullPath)) {
        $content = file_get_contents($fullPath);
        
        // Check for unified CSS
        $hasUnifiedCSS = strpos($content, 'admin-faculty.css') !== false;
        
        // Check for duplicate styles (inline <style> tags)
        $hasInlineStyles = preg_match('/<style\s*[^>]*>/', $content);
        
        // Check for consistent float classes
        $hasFloatEnd = strpos($content, 'float-end') !== false;
        $hasOldFloatRight = strpos($content, 'float-right') !== false && strpos($content, 'float-end') === false;
        
        // Check for collapse-btn class
        $hasCollapseBtn = strpos($content, 'collapse-btn') !== false;
        
        echo "<h3>$name Page</h3>";
        echo "<ul>";
        echo "<li>Uses unified CSS: " . ($hasUnifiedCSS ? "✅ YES" : "❌ NO") . "</li>";
        echo "<li>No inline styles: " . (!$hasInlineStyles ? "✅ YES" : "❌ NO") . "</li>";
        echo "<li>Uses float-end: " . ($hasFloatEnd ? "✅ YES" : "❌ NO") . "</li>";
        echo "<li>No old float-right: " . (!$hasOldFloatRight ? "✅ YES" : "❌ NO") . "</li>";
        echo "<li>Has collapse-btn class: " . ($hasCollapseBtn ? "✅ YES" : "❌ NO") . "</li>";
        echo "</ul>";
        
    } else {
        echo "<h3>$name Page: ❌ FILE NOT FOUND</h3>";
    }
}

// Check unified CSS file
$cssPath = __DIR__ . '/public/assets/css/admin-faculty.css';
if (file_exists($cssPath)) {
    $cssContent = file_get_contents($cssPath);
    $cssSize = filesize($cssPath);
    
    echo "<h3>Unified CSS File</h3>";
    echo "<ul>";
    echo "<li>File exists: ✅ YES</li>";
    echo "<li>File size: " . number_format($cssSize) . " bytes</li>";
    echo "<li>Has float classes: " . (strpos($cssContent, 'float-end') !== false ? "✅ YES" : "❌ NO") . "</li>";
    echo "<li>Has collapse-btn: " . (strpos($cssContent, 'collapse-btn') !== false ? "✅ YES" : "❌ NO") . "</li>";
    echo "<li>Has card styling: " . (strpos($cssContent, '.card {') !== false ? "✅ YES" : "❌ NO") . "</li>";
    echo "<li>Has button styling: " . (strpos($cssContent, '.btn {') !== false ? "✅ YES" : "❌ NO") . "</li>";
    echo "</ul>";
} else {
    echo "<h3>❌ Unified CSS file not found!</h3>";
}

echo "<hr>";
echo "<p><strong>Summary:</strong> All faculty admin pages should show ✅ YES for all checks above for clean, consistent, professional styling.</p>";
?>