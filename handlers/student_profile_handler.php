<?php
require_once __DIR__ . '/../app/models/StudentProfile.php';
require_once __DIR__ . '/../app/models/Model.php';

// Handle profile save - check for POST data and user_id
if ($_POST && isset($_POST['user_id'])) {
    $userId = (int)$_POST['user_id'];
    
    // Validate required fields
    $errors = [];
    $program = trim($_POST['program'] ?? '');
    $yearLevel = trim($_POST['year_level'] ?? '');
    
    if (empty($program)) {
        $errors[] = 'Program is required';
    }
    if (empty($yearLevel)) {
        $errors[] = 'Year level is required';
    }

    if (empty($errors)) {
        try {
            // Get current user data (names won't be updated)
            class _DBX extends Model { public function d(){ return parent::db(); } }
            $db = (new _DBX())->d();
            
            // Get existing user data to preserve name fields
            $existingUser = $db->prepare("SELECT first_name, last_name FROM users WHERE id = ?");
            $existingUser->execute([$userId]);
            $userData = $existingUser->fetch(PDO::FETCH_ASSOC);
            
            // Prepare data for users table update (excluding name fields)
            $userUpdateData = [
                'email' => trim($_POST['email'] ?? ''),
                'phone' => trim($_POST['phone'] ?? ''),
                'program' => $program,
                'year_level' => $yearLevel
            ];
            
            // Prepare data for student_profiles table (extended info)
            $profileData = [
                'bio' => trim($_POST['bio'] ?? ''),
                'skills' => trim($_POST['skills'] ?? ''),
                'linkedin_url' => trim($_POST['linkedin_url'] ?? ''),
                'github_url' => trim($_POST['github_url'] ?? ''),
                'portfolio_url' => trim($_POST['website_url'] ?? '') // Map website_url to portfolio_url
            ];

            // Connect to database
            $db->beginTransaction();
            
            // Update users table (excluding name fields)
            $userUpdateSQL = "UPDATE users SET 
                email = ?, phone = ?, program = ?, year_level = ?, 
                address = ?, updated_at = CURRENT_TIMESTAMP 
                WHERE id = ?";
            $userStmt = $db->prepare($userUpdateSQL);
            $userStmt->execute([
                $userUpdateData['email'], 
                $userUpdateData['phone'], 
                $userUpdateData['program'], 
                $userUpdateData['year_level'], 
                trim($_POST['location'] ?? ''), // Store location in users.address
                $userId
            ]);
            
            // Check if student_profiles record exists
            $checkStmt = $db->prepare("SELECT user_id FROM student_profiles WHERE user_id = ?");
            $checkStmt->execute([$userId]);
            $profileExists = $checkStmt->fetch();
            
            if ($profileExists) {
                // Update student_profiles (preserve existing names, update email)
                $profileUpdateSQL = "UPDATE student_profiles SET 
                    email = ?, bio = ?, skills = ?, linkedin_url = ?, 
                    github_url = ?, portfolio_url = ?, updated_at = CURRENT_TIMESTAMP 
                    WHERE user_id = ?";
                $profileStmt = $db->prepare($profileUpdateSQL);
                $profileStmt->execute([
                    $userUpdateData['email'],
                    $profileData['bio'], 
                    $profileData['skills'], 
                    $profileData['linkedin_url'], 
                    $profileData['github_url'], 
                    $profileData['portfolio_url'], 
                    $userId
                ]);
            } else {
                // Insert into student_profiles (use existing names from users table)
                $profileInsertSQL = "INSERT INTO student_profiles 
                    (user_id, first_name, last_name, email, bio, skills, linkedin_url, github_url, portfolio_url, created_at, updated_at) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)";
                $profileStmt = $db->prepare($profileInsertSQL);
                $profileStmt->execute([
                    $userId,
                    $userData['first_name'],
                    $userData['last_name'],
                    $userUpdateData['email'],
                    $profileData['bio'], 
                    $profileData['skills'], 
                    $profileData['linkedin_url'], 
                    $profileData['github_url'], 
                    $profileData['portfolio_url']
                ]);
            }
            
            $db->commit();
            
            // Redirect back to profile with success message
            header('Location: /adamson-ccit/public/index.php?page=student_profile&saved=1');
            exit;
            
        } catch (Exception $e) {
            if (isset($db)) {
                $db->rollback();
            }
            error_log("Profile update error: " . $e->getMessage());
            $error = 'Failed to save profile. Please try again.';
        }
    } else {
        $error = implode(', ', $errors);
    }
    
    // If we get here, there was an error
    header('Location: /adamson-ccit/public/index.php?page=student_profile&error=' . urlencode($error));
    exit;
}
?>