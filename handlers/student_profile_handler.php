<?php
require_once __DIR__ . '/../../models/StudentProfile.php';

// Handle profile save
if ($_POST && isset($_POST['page']) && $_POST['page'] === 'student_profile_save') {
    $userId = (int)$_POST['user_id'];
    $profileData = [
        'full_name' => trim($_POST['full_name'] ?? ''),
        'program' => trim($_POST['program'] ?? ''),
        'year_level' => trim($_POST['year_level'] ?? ''),
        'bio' => trim($_POST['bio'] ?? ''),
        'skills' => trim($_POST['skills'] ?? ''),
        'email' => trim($_POST['email'] ?? ''),
        'phone' => trim($_POST['phone'] ?? ''),
        'location' => trim($_POST['location'] ?? ''),
        'linkedin_url' => trim($_POST['linkedin_url'] ?? ''),
        'github_url' => trim($_POST['github_url'] ?? ''),
        'website_url' => trim($_POST['website_url'] ?? ''),
        'updated_at' => date('Y-m-d H:i:s')
    ];

    // Validate required fields
    $errors = [];
    if (empty($profileData['full_name'])) {
        $errors[] = 'Full name is required';
    }
    if (empty($profileData['program'])) {
        $errors[] = 'Program is required';
    }
    if (empty($profileData['year_level'])) {
        $errors[] = 'Year level is required';
    }

    if (empty($errors)) {
        $studentProfile = new StudentProfile();
        
        // Check if profile exists
        $existingProfile = $studentProfile->getByUserId($userId);
        
        if ($existingProfile) {
            // Update existing profile
            $success = $studentProfile->update($userId, $profileData);
        } else {
            // Create new profile
            $profileData['user_id'] = $userId;
            $profileData['created_at'] = date('Y-m-d H:i:s');
            $success = $studentProfile->create($profileData);
        }

        if ($success) {
            // Redirect back to profile with success message
            header('Location: /adamson-ccit/public/index.php?page=student_profile&saved=1');
            exit;
        } else {
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