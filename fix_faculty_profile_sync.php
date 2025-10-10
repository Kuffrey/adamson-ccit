<?php
require_once 'app/models/FacultyProfile.php';

echo "<h2>Fixing Faculty Profile Sync Issue</h2>\n";

try {
    $pdo = new PDO("mysql:host=localhost;dbname=adamson_ccit", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get faculty users who don't have faculty_profile records
    $stmt = $pdo->query("
        SELECT u.id, u.username, u.first_name, u.last_name 
        FROM users u 
        WHERE u.role = 'faculty' 
        AND u.id NOT IN (SELECT id FROM faculty_profile WHERE id IS NOT NULL)
    ");
    $missingProfiles = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($missingProfiles)) {
        echo "✅ No missing faculty profiles found<br>\n";
    } else {
        echo "<h3>Creating missing faculty profiles</h3>\n";
        
        foreach ($missingProfiles as $faculty) {
            echo "<p>Creating profile for: {$faculty['first_name']} {$faculty['last_name']} (ID: {$faculty['id']})</p>\n";
            
            // Create faculty profile data
            $profileData = [
                'prefix' => 'Mr.', // Default prefix, can be updated later
                'first_name' => $faculty['first_name'],
                'surname' => $faculty['last_name'],
                'dept' => 'cs', // Default department, can be updated later
                'role' => 'full', // Default role, can be updated later
                'title' => 'Faculty Member'
            ];
            
            try {
                // Create the profile
                $createdId = FacultyProfile::create($profileData);
                
                if ($createdId) {
                    // Now we need to update the ID to match the user ID
                    // This is a bit tricky because of auto-increment, so we need to do a direct SQL update
                    $updateSql = "UPDATE faculty_profile SET id = ? WHERE id = ?";
                    $updateStmt = $pdo->prepare($updateSql);
                    $updateResult = $updateStmt->execute([$faculty['id'], $createdId]);
                    
                    if ($updateResult) {
                        echo "✅ Faculty profile created with ID {$faculty['id']}<br>\n";
                        
                        // Delete the old record with auto-generated ID
                        $deleteSql = "DELETE FROM faculty_profile WHERE id = ? AND id != ?";
                        $deleteStmt = $pdo->prepare($deleteSql);
                        $deleteStmt->execute([$createdId, $faculty['id']]);
                        
                    } else {
                        echo "⚠️ Profile created but ID sync failed<br>\n";
                    }
                } else {
                    echo "❌ Failed to create profile<br>\n";
                }
                
            } catch (Exception $e) {
                echo "❌ Error creating profile: " . $e->getMessage() . "<br>\n";
                
                // Alternative approach: Direct SQL insert with specific ID
                try {
                    echo "Trying direct SQL insert...<br>\n";
                    $directSql = "INSERT INTO faculty_profile (id, name, prefix, first_name, surname, dept, role, title, ordering, avatar_initials) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    $directStmt = $pdo->prepare($directSql);
                    
                    $fullName = FacultyProfile::buildFullName([
                        'prefix' => 'Mr.',
                        'first_name' => $faculty['first_name'],
                        'surname' => $faculty['last_name']
                    ]);
                    
                    $initials = FacultyProfile::generateAvatarInitials($faculty['first_name'], $faculty['last_name']);
                    
                    $directResult = $directStmt->execute([
                        $faculty['id'],
                        $fullName,
                        'Mr.',
                        $faculty['first_name'],
                        $faculty['last_name'],
                        'cs',
                        'full',
                        'Faculty Member',
                        999,
                        $initials
                    ]);
                    
                    if ($directResult) {
                        echo "✅ Faculty profile created via direct SQL with ID {$faculty['id']}<br>\n";
                    } else {
                        echo "❌ Direct SQL insert also failed<br>\n";
                    }
                    
                } catch (Exception $directError) {
                    echo "❌ Direct SQL error: " . $directError->getMessage() . "<br>\n";
                }
            }
        }
    }
    
    // Verify the fix
    echo "<h3>Verification</h3>\n";
    $verifyStmt = $pdo->query("
        SELECT u.id, u.username, u.first_name, u.last_name, 
               fp.id as profile_id, fp.name as profile_name
        FROM users u 
        LEFT JOIN faculty_profile fp ON u.id = fp.id
        WHERE u.role = 'faculty'
    ");
    $verification = $verifyStmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' style='border-collapse: collapse;'>\n";
    echo "<tr><th>User ID</th><th>Username</th><th>Name</th><th>Profile ID</th><th>Profile Name</th><th>Status</th></tr>\n";
    foreach ($verification as $v) {
        $status = $v['profile_id'] ? '✅ Synced' : '❌ Missing';
        echo "<tr>";
        echo "<td>{$v['id']}</td>";
        echo "<td>{$v['username']}</td>";
        echo "<td>{$v['first_name']} {$v['last_name']}</td>";
        echo "<td>{$v['profile_id']}</td>";
        echo "<td>{$v['profile_name']}</td>";
        echo "<td>$status</td>";
        echo "</tr>\n";
    }
    echo "</table>\n<br>";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>\n";
}

echo "<h3>Next Steps</h3>\n";
echo "<p>If the verification shows ✅ Synced, try creating an announcement submission again.</p>\n";
?>