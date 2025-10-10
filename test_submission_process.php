<?php
session_start();

// Simulate faculty login for testing
$_SESSION['user'] = [
    'id' => 3,
    'username' => 'faculty',
    'role' => 'faculty'
];

echo "<h2>Testing Faculty Submission Process</h2>\n";

try {
    // Test 1: Check if we can create an announcement
    echo "<h3>Test 1: Creating a test announcement</h3>\n";
    require_once __DIR__ . '/app/models/Announcement.php';
    
    $testAnnouncementId = Announcement::create(
        'Test Announcement for Submission',
        'This is a test announcement content to check if submissions work.',
        'draft',
        'general',
        date('Y-m-d'),
        null
    );
    
    if ($testAnnouncementId) {
        echo "✅ Test announcement created with ID: $testAnnouncementId<br>\n";
        
        // Test 2: Try to create a submission
        echo "<h3>Test 2: Creating submission for announcement</h3>\n";
        require_once __DIR__ . '/app/models/FacultySubmissions.php';
        
        $faculty_id = $_SESSION['user']['id'];
        echo "Faculty ID: $faculty_id<br>\n";
        
        try {
            $submissionResult = FacultySubmissions::createFromAnnouncement($testAnnouncementId, $faculty_id);
            
            if ($submissionResult) {
                echo "✅ Submission created successfully!<br>\n";
                
                // Test 3: Verify the submission exists
                echo "<h3>Test 3: Verifying submission exists</h3>\n";
                $submissions = FacultySubmissions::getByFacultyId($faculty_id);
                $latestSubmission = null;
                foreach ($submissions as $sub) {
                    if ($sub['submission_type'] === 'announcement' && $sub['related_item_id'] == $testAnnouncementId) {
                        $latestSubmission = $sub;
                        break;
                    }
                }
                
                if ($latestSubmission) {
                    echo "✅ Submission found in database:<br>\n";
                    echo "<ul>\n";
                    echo "<li>ID: {$latestSubmission['id']}</li>\n";
                    echo "<li>Faculty ID: {$latestSubmission['faculty_id']}</li>\n";
                    echo "<li>Type: {$latestSubmission['submission_type']}</li>\n";
                    echo "<li>Title: {$latestSubmission['title']}</li>\n";
                    echo "<li>Status: {$latestSubmission['status']}</li>\n";
                    echo "<li>Submitted At: {$latestSubmission['submitted_at']}</li>\n";
                    echo "</ul>\n";
                } else {
                    echo "❌ Submission not found in database<br>\n";
                }
            } else {
                echo "❌ Submission creation returned false<br>\n";
            }
        } catch (Exception $e) {
            echo "❌ Error creating submission: " . $e->getMessage() . "<br>\n";
            echo "Stack trace: " . $e->getTraceAsString() . "<br>\n";
        }
        
        // Clean up: Delete test announcement
        echo "<h3>Cleanup: Deleting test announcement</h3>\n";
        try {
            Announcement::delete($testAnnouncementId);
            echo "✅ Test announcement deleted<br>\n";
        } catch (Exception $e) {
            echo "⚠️ Could not delete test announcement: " . $e->getMessage() . "<br>\n";
        }
        
    } else {
        echo "❌ Failed to create test announcement<br>\n";
    }
    
    // Test 4: Check database connection in FacultySubmissions
    echo "<h3>Test 4: Testing database connection</h3>\n";
    try {
        $pdo = new PDO("mysql:host=localhost;dbname=adamson_ccit", "root", "");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM faculty_submissions");
        $result = $stmt->fetch();
        echo "✅ Database connection works. Total submissions: " . $result['count'] . "<br>\n";
        
        // Check if the faculty_id exists in users table
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? AND role = 'faculty'");
        $stmt->execute([$faculty_id]);
        $faculty = $stmt->fetch();
        
        if ($faculty) {
            echo "✅ Faculty user found: {$faculty['username']} ({$faculty['first_name']} {$faculty['last_name']})<br>\n";
        } else {
            echo "❌ Faculty user with ID $faculty_id not found<br>\n";
        }
        
    } catch (PDOException $e) {
        echo "❌ Database connection error: " . $e->getMessage() . "<br>\n";
    }
    
} catch (Exception $e) {
    echo "❌ General error: " . $e->getMessage() . "<br>\n";
    echo "Stack trace: " . $e->getTraceAsString() . "<br>\n";
}

echo "<h3>Summary</h3>\n";
echo "<p>If you see ✅ marks above, the submission system is working correctly.</p>\n";
echo "<p>If you see ❌ marks, there are issues that need to be fixed.</p>\n";
?>