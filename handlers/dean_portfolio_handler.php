<?php
require_once __DIR__ . '/../app/lib/Auth.php';
require_once __DIR__ . '/../app/models/Model.php';
require_once __DIR__ . '/../app/models/DeanPortfolio.php';

// Ensure user is authenticated as dean
Auth::requireRole(['dean'], '/adamson-ccit/public/index.php?page=login');

$user = Auth::user() ?? [];
$deanId = $user['id'] ?? null;

if (!$deanId) {
    header('Location: /adamson-ccit/public/index.php?page=login');
    exit;
}

// Handle all portfolio actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle Edit Profile Information
    if ($_POST['action'] === 'edit_profile') {
        $userId = intval($_POST['user_id'] ?? 0);
        $data = [
            'full_name' => trim($_POST['full_name'] ?? ''),
            'employee_id' => trim($_POST['employee_id'] ?? ''),
            'work_email' => trim($_POST['work_email'] ?? ''),
            'position' => trim($_POST['position'] ?? ''),
            'department' => trim($_POST['department'] ?? ''),
            'employment_type' => trim($_POST['employment_type'] ?? ''),
            'date_hired' => $_POST['date_hired'] ?? null,
            'office_location' => trim($_POST['office_location'] ?? ''),
            'status' => trim($_POST['status'] ?? ''),
            'bio' => trim($_POST['bio'] ?? ''),
            'specializations' => trim($_POST['specializations'] ?? ''),
            'languages' => trim($_POST['languages'] ?? '')
        ];

        // Handle profile photo upload
        if (!empty($_FILES['profile_photo']['name']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../public/assets/images/';
            $ext = strtolower(pathinfo($_FILES['profile_photo']['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            if (in_array($ext, $allowed)) {
                $filename = 'profile_' . $userId . '_' . time() . '.' . $ext;
                $targetPath = $uploadDir . $filename;
                if (move_uploaded_file($_FILES['profile_photo']['tmp_name'], $targetPath)) {
                    $data['profile_photo'] = '/adamson-ccit/public/assets/images/' . $filename;
                }
            }
        } else {
            // Keep existing photo if not uploading new one
            $profile = DeanPortfolio::getProfile($userId);
            if (!empty($profile['profile_photo'])) {
                $data['profile_photo'] = $profile['profile_photo'];
            }
        }

        $result = DeanPortfolio::updateProfile($userId, $data);
        if ($result) {
            header('Location: /adamson-ccit/app/views/dean/dean_portfolio.php?saved=1&folder=general');
            exit;
        } else {
            header('Location: /adamson-ccit/app/views/dean/dean_portfolio.php?error=Unable to save profile&folder=general');
            exit;
        }
    }

    try {
        $action = $_POST['action'];
        
        switch ($action) {
            case 'add_certification':
                $result = DeanPortfolio::createCertification($deanId, $_POST);
                header('Location: /adamson-ccit/public/index.php?page=dean_portfolio&folder=certifications&saved=1');
                exit;
                
            case 'edit_certification':
                $certId = $_POST['cert_id'] ?? $_POST['id'];
                $result = DeanPortfolio::updateCertification($certId, $deanId, $_POST);
                header('Location: /adamson-ccit/public/index.php?page=dean_portfolio&folder=certifications&saved=1');
                exit;
                
            case 'delete_certification':
                $certId = $_POST['cert_id'] ?? $_POST['id'];
                $result = DeanPortfolio::deleteCertification($certId, $deanId);
                header('Location: /adamson-ccit/public/index.php?page=dean_portfolio&folder=certifications&deleted=1');
                exit;
                
            case 'add_experience':
                $result = DeanPortfolio::createExperience($deanId, $_POST);
                header('Location: /adamson-ccit/public/index.php?page=dean_portfolio&folder=experience&saved=1');
                exit;
                
            case 'edit_experience':
                $result = DeanPortfolio::updateExperience($_POST['exp_id'], $deanId, $_POST);
                header('Location: /adamson-ccit/public/index.php?page=dean_portfolio&folder=experience&saved=1');
                exit;
                
            case 'delete_experience':
                $result = DeanPortfolio::deleteExperience($_POST['exp_id'], $deanId);
                header('Location: /adamson-ccit/public/index.php?page=dean_portfolio&folder=experience&deleted=1');
                exit;
                
            case 'add_education':
                $result = DeanPortfolio::createEducation($deanId, $_POST);
                header('Location: /adamson-ccit/public/index.php?page=dean_portfolio&folder=education&saved=1');
                exit;
                
            case 'edit_education':
                $result = DeanPortfolio::updateEducation($_POST['edu_id'], $deanId, $_POST);
                header('Location: /adamson-ccit/public/index.php?page=dean_portfolio&folder=education&saved=1');
                exit;
                
            case 'delete_education':
                $result = DeanPortfolio::deleteEducation($_POST['edu_id'], $deanId);
                header('Location: /adamson-ccit/public/index.php?page=dean_portfolio&folder=education&deleted=1');
                exit;
                
            case 'edit_personal':
                $result = DeanPortfolio::updatePersonal($deanId, $_POST);
                header('Location: /adamson-ccit/public/index.php?page=dean_portfolio&folder=personal&saved=1');
                exit;
                
            case 'add_research':
                $result = DeanPortfolio::createResearch($deanId, $_POST);
                header('Location: /adamson-ccit/public/index.php?page=dean_portfolio&folder=research&saved=1');
                exit;
                
            case 'edit_research':
                $result = DeanPortfolio::updateResearch($_POST['research_id'], $deanId, $_POST);
                header('Location: /adamson-ccit/public/index.php?page=dean_portfolio&folder=research&saved=1');
                exit;
                
            case 'delete_research':
                $result = DeanPortfolio::deleteResearch($_POST['research_id'], $deanId);
                header('Location: /adamson-ccit/public/index.php?page=dean_portfolio&folder=research&deleted=1');
                exit;
                
            case 'add_training':
                $result = DeanPortfolio::createTraining($deanId, $_POST);
                header('Location: /adamson-ccit/public/index.php?page=dean_portfolio&folder=trainings&saved=1');
                exit;
            case 'edit_training':
                $result = DeanPortfolio::updateTraining($_POST['training_id'], $deanId, $_POST);
                header('Location: /adamson-ccit/public/index.php?page=dean_portfolio&folder=trainings&saved=1');
                exit;
            case 'delete_training':
                $result = DeanPortfolio::deleteTraining($_POST['training_id'], $deanId);
                header('Location: /adamson-ccit/public/index.php?page=dean_portfolio&folder=trainings&deleted=1');
                exit;

            case 'add_performance':
                $result = DeanPortfolio::createPerformance($deanId, $_POST);
                header('Location: /adamson-ccit/public/index.php?page=dean_portfolio&folder=performance&saved=1');
                exit;
            case 'edit_performance':
                $result = DeanPortfolio::updatePerformance($_POST['performance_id'], $deanId, $_POST);
                header('Location: /adamson-ccit/public/index.php?page=dean_portfolio&folder=performance&saved=1');
                exit;
            case 'delete_performance':
                $result = DeanPortfolio::deletePerformance($_POST['performance_id'], $deanId);
                header('Location: /adamson-ccit/public/index.php?page=dean_portfolio&folder=performance&deleted=1');
                exit;

            case 'add_award':
                $result = DeanPortfolio::createAward($deanId, $_POST);
                header('Location: /adamson-ccit/public/index.php?page=dean_portfolio&folder=awards&saved=1');
                exit;
            case 'edit_award':
                $result = DeanPortfolio::updateAward($_POST['award_id'], $deanId, $_POST);
                header('Location: /adamson-ccit/public/index.php?page=dean_portfolio&folder=awards&saved=1');
                exit;
            case 'delete_award':
                $result = DeanPortfolio::deleteAward($_POST['award_id'], $deanId);
                header('Location: /adamson-ccit/public/index.php?page=dean_portfolio&folder=awards&deleted=1');
                exit;
                
            default:
                throw new Exception('Invalid action: ' . $action);
        }
        
    } catch (Exception $e) {
        error_log("Dean portfolio handler error: " . $e->getMessage());
        $error = 'Failed to process request: ' . $e->getMessage();
        header('Location: /adamson-ccit/public/index.php?page=dean_portfolio&error=' . urlencode($error));
        exit;
    }
}

// If no valid POST action, redirect to portfolio
header('Location: /adamson-ccit/public/index.php?page=dean_portfolio');
exit;
?>