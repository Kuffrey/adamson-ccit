<?php
require_once __DIR__ . '/../app/lib/Auth.php';
require_once __DIR__ . '/../app/models/Model.php';
require_once __DIR__ . '/../app/models/FacultyPortfolio.php';

// Ensure user is authenticated as faculty
Auth::requireRole(['faculty'], '/adamson-ccit/public/index.php?page=login');

$user = Auth::user() ?? [];
$facultyId = $user['id'] ?? null;

if (!$facultyId) {
    header('Location: /adamson-ccit/public/index.php?page=login');
    exit;
}

// Sanitize input function
function sanitizeInput($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

// Validate file upload
function validateFileUpload($file, $allowedExtensions) {
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedExtensions)) {
        throw new Exception("Invalid file type. Allowed types: " . implode(', ', $allowedExtensions));
    }
    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new Exception("File upload error: " . $file['error']);
    }
    return $ext;
}

// Handle all portfolio actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $action = sanitizeInput($_POST['action'] ?? '');

        switch ($action) {
            case 'edit_profile':
                $userId = intval($_POST['user_id'] ?? 0);
                $data = [
                    'full_name' => sanitizeInput($_POST['full_name'] ?? ''),
                    'employee_id' => sanitizeInput($_POST['employee_id'] ?? ''),
                    'work_email' => sanitizeInput($_POST['work_email'] ?? ''),
                    'position' => sanitizeInput($_POST['position'] ?? ''),
                    'department' => sanitizeInput($_POST['department'] ?? ''),
                    'employment_type' => sanitizeInput($_POST['employment_type'] ?? ''),
                    'date_hired' => sanitizeInput($_POST['date_hired'] ?? null),
                    'office_location' => sanitizeInput($_POST['office_location'] ?? ''),
                    'status' => sanitizeInput($_POST['status'] ?? ''),
                    'bio' => sanitizeInput($_POST['bio'] ?? ''),
                    'specializations' => sanitizeInput($_POST['specializations'] ?? ''),
                    'languages' => sanitizeInput($_POST['languages'] ?? '')
                ];

                // Handle profile photo upload
                if (!empty($_FILES['profile_photo']['name'])) {
                    $uploadDir = __DIR__ . '/../uploads/profile_photos/';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }

                    $fileName = 'profile_' . $userId . '_' . time() . '.' . pathinfo($_FILES['profile_photo']['name'], PATHINFO_EXTENSION);
                    $targetFile = $uploadDir . $fileName;

                    if (move_uploaded_file($_FILES['profile_photo']['tmp_name'], $targetFile)) {
                        $data['profile_photo'] = '/adamson-ccit/uploads/profile_photos/' . $fileName;
                    }
                } else {
                    // Keep existing photo if not uploading new one
                    $profile = FacultyPortfolio::getProfile($userId);
                    $data['profile_photo'] = $profile['profile_photo'] ?? null;
                }

                $result = FacultyPortfolio::updateProfile($userId, $data);
                if ($result) {
                    header('Location: /adamson-ccit/public/index.php?page=faculty_portfolio&saved=1&folder=general');
                } else {
                    throw new Exception("Unable to save profile.");
                }
                exit;

            case 'remove_profile_photo':
                $userId = intval($_POST['user_id'] ?? 0);

                // Get the current profile to retrieve the photo path
                $profile = FacultyPortfolio::getProfile($userId);

                if (!empty($profile['profile_photo'])) {
                    $filePath = __DIR__ . '/../..' . $profile['profile_photo']; // Adjust path to match your directory structure

                    // Check if the file exists and delete it
                    if (file_exists($filePath)) {
                        if (!unlink($filePath)) {
                            header('Location: /adamson-ccit/public/index.php?page=faculty_portfolio&error=Failed to delete image');
                            exit;
                        }
                    }
                }

                // Remove the profile photo reference from the database
                FacultyPortfolio::updateProfile($userId, ['profile_photo' => null]);

                header('Location: /adamson-ccit/public/index.php?page=faculty_portfolio&saved=1');
                exit;

            case 'add_certification':
                $result = FacultyPortfolio::createCertification($facultyId, $_POST);
                header('Location: /adamson-ccit/public/index.php?page=faculty_portfolio&folder=certifications&saved=1');
                exit;

            case 'edit_certification':
                $certId = intval($_POST['cert_id'] ?? 0);
                $result = FacultyPortfolio::updateCertification($certId, $facultyId, $_POST);
                header('Location: /adamson-ccit/public/index.php?page=faculty_portfolio&folder=certifications&saved=1');
                exit;

            case 'delete_certification':
                $certId = intval($_POST['cert_id'] ?? 0);
                $result = FacultyPortfolio::deleteCertification($certId, $facultyId);
                header('Location: /adamson-ccit/public/index.php?page=faculty_portfolio&folder=certifications&deleted=1');
                exit;

            case 'add_experience':
                $result = FacultyPortfolio::createExperience($facultyId, $_POST);
                header('Location: /adamson-ccit/public/index.php?page=faculty_portfolio&folder=experience&saved=1');
                exit;
                
            case 'edit_experience':
                $result = FacultyPortfolio::updateExperience($_POST['exp_id'], $facultyId, $_POST);
                header('Location: /adamson-ccit/public/index.php?page=faculty_portfolio&folder=experience&saved=1');
                exit;
                
            case 'delete_experience':
                $result = FacultyPortfolio::deleteExperience($_POST['exp_id'], $facultyId);
                header('Location: /adamson-ccit/public/index.php?page=faculty_portfolio&folder=experience&deleted=1');
                exit;
                
            case 'add_education':
                $result = FacultyPortfolio::createEducation($facultyId, $_POST);
                header('Location: /adamson-ccit/public/index.php?page=faculty_portfolio&folder=education&saved=1');
                exit;
                
            case 'edit_education':
                $result = FacultyPortfolio::updateEducation($_POST['edu_id'], $facultyId, $_POST);
                header('Location: /adamson-ccit/public/index.php?page=faculty_portfolio&folder=education&saved=1');
                exit;
                
            case 'delete_education':
                $result = FacultyPortfolio::deleteEducation($_POST['edu_id'], $facultyId);
                header('Location: /adamson-ccit/public/index.php?page=faculty_portfolio&folder=education&deleted=1');
                exit;
                
            case 'edit_personal':
                $result = FacultyPortfolio::updatePersonal($facultyId, $_POST);
                header('Location: /adamson-ccit/public/index.php?page=faculty_portfolio&folder=personal&saved=1');
                exit;
                
            case 'add_research':
                $result = FacultyPortfolio::createResearch($facultyId, $_POST);
                header('Location: /adamson-ccit/public/index.php?page=faculty_portfolio&folder=research&saved=1');
                exit;
                
            case 'edit_research':
                $result = FacultyPortfolio::updateResearch($_POST['research_id'], $facultyId, $_POST);
                header('Location: /adamson-ccit/public/index.php?page=faculty_portfolio&folder=research&saved=1');
                exit;
                
            case 'delete_research':
                $result = FacultyPortfolio::deleteResearch($_POST['research_id'], $facultyId);
                header('Location: /adamson-ccit/public/index.php?page=faculty_portfolio&folder=research&deleted=1');
                exit;
                
            case 'add_training':
                $result = FacultyPortfolio::createTraining($facultyId, $_POST);
                header('Location: /adamson-ccit/public/index.php?page=faculty_portfolio&folder=trainings&saved=1');
                exit;
            case 'edit_training':
                $result = FacultyPortfolio::updateTraining($_POST['training_id'], $facultyId, $_POST);
                header('Location: /adamson-ccit/public/index.php?page=faculty_portfolio&folder=trainings&saved=1');
                exit;
            case 'delete_training':
                $result = FacultyPortfolio::deleteTraining($_POST['training_id'], $facultyId);
                header('Location: /adamson-ccit/public/index.php?page=faculty_portfolio&folder=trainings&deleted=1');
                exit;

            case 'add_performance':
                $result = FacultyPortfolio::createPerformance($facultyId, $_POST);
                header('Location: /adamson-ccit/public/index.php?page=faculty_portfolio&folder=performance&saved=1');
                exit;
            case 'edit_performance':
                $result = FacultyPortfolio::updatePerformance($_POST['performance_id'], $facultyId, $_POST);
                header('Location: /adamson-ccit/public/index.php?page=faculty_portfolio&folder=performance&saved=1');
                exit;
            case 'delete_performance':
                $result = FacultyPortfolio::deletePerformance($_POST['performance_id'], $facultyId);
                header('Location: /adamson-ccit/public/index.php?page=faculty_portfolio&folder=performance&deleted=1');
                exit;

            case 'add_award':
                $result = FacultyPortfolio::createAward($facultyId, $_POST);
                header('Location: /adamson-ccit/public/index.php?page=faculty_portfolio&folder=awards&saved=1');
                exit;
            case 'edit_award':
                $result = FacultyPortfolio::updateAward($_POST['award_id'], $facultyId, $_POST);
                header('Location: /adamson-ccit/public/index.php?page=faculty_portfolio&folder=awards&saved=1');
                exit;
            case 'delete_award':
                $result = FacultyPortfolio::deleteAward($_POST['award_id'], $facultyId);
                header('Location: /adamson-ccit/public/index.php?page=faculty_portfolio&folder=awards&deleted=1');
                exit;
                
            default:
                throw new Exception('Invalid action: ' . $action);
        }
    } catch (Exception $e) {
        error_log("Faculty portfolio handler error: " . $e->getMessage());
        $error = 'Failed to process request: ' . $e->getMessage();
        header('Location: /adamson-ccit/public/index.php?page=faculty_portfolio&error=' . urlencode($error));
        exit;
    }
}

// If no valid POST action, redirect to portfolio
header('Location: /adamson-ccit/public/index.php?page=faculty_portfolio');
exit;
?>