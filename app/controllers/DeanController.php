<?php
declare(strict_types=1);

require_once __DIR__ . '/../models/FacultySubmissions.php';

class DeanController {
    private $facultySubmissions;
    
    public function __construct() {
        $this->facultySubmissions = new FacultySubmissions();
    }
    
    public function dashboard(): string {
        ob_start();
        include __DIR__ . '/../views/dean_dashboard.php';
        return ob_get_clean();
    }

    // Queue of items that require dean approval (featured research, homepage news, etc.)
    public function approvals(): string {
        ob_start();
        include __DIR__ . '/../views/dean_approvals.php';
        return ob_get_clean();
    }

    // Optional convenience wrappers for dean-specific “create” pages.
    // These can post to the existing admin manage routes (action=new/create).
    public function createNews(): string {
        ob_start();
        include __DIR__ . '/../views/dean_create_news.php';
        return ob_get_clean();
    }

    public function createEvent(): string {
        ob_start();
        include __DIR__ . '/../views/dean_create_event.php';
        return ob_get_clean();
    }

    public function createAnnouncement(): string {
        ob_start();
        include __DIR__ . '/../views/dean_create_announcement.php';
        return ob_get_clean();
    }

    // Handle faculty submission approval/rejection actions (research and news only)
    public function manageFacultyProfiles(): void {
        $message = '';
        $messageType = 'success';
        
        // Handle POST actions
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
            try {
                switch ($_POST['action']) {
                    case 'approve_submission':
                        if (isset($_POST['submission_id'])) {
                            $submissionId = (int)$_POST['submission_id'];
                            
                            // Get submission type to ensure it's research or news only
                            $submission = FacultySubmissions::getById($submissionId);
                            if ($submission && in_array($submission['submission_type'], ['research', 'news'])) {
                                $result = $this->facultySubmissions->approveSubmission($submissionId);
                                $message = $result ? 'Submission approved successfully!' : 'Failed to approve submission.';
                                $messageType = $result ? 'success' : 'danger';
                            } else {
                                $message = 'Invalid submission type. Dean can only approve research and news submissions.';
                                $messageType = 'warning';
                            }
                        }
                        break;
                        
                    case 'reject_submission':
                        if (isset($_POST['submission_id']) && isset($_POST['rejection_notes'])) {
                            $submissionId = (int)$_POST['submission_id'];
                            
                            // Get submission type to ensure it's research or news only
                            $submission = FacultySubmissions::getById($submissionId);
                            if ($submission && in_array($submission['submission_type'], ['research', 'news'])) {
                                $result = $this->facultySubmissions->rejectSubmission(
                                    $submissionId, 
                                    trim($_POST['rejection_notes'])
                                );
                                $message = $result ? 'Submission rejected successfully.' : 'Failed to reject submission.';
                                $messageType = $result ? 'success' : 'danger';
                            } else {
                                $message = 'Invalid submission type. Dean can only manage research and news submissions.';
                                $messageType = 'warning';
                            }
                        }
                        break;
                        
                    default:
                        $message = 'Unknown action requested.';
                        $messageType = 'warning';
                }
            } catch (Exception $e) {
                $message = 'Error processing request: ' . $e->getMessage();
                $messageType = 'danger';
            }
        }
        
        // Get data for display (research and news only)
        $pendingCounts = $this->facultySubmissions->getPendingCountsByType();
        $facultyWithSubmissions = $this->facultySubmissions->getSubmissionsByFacultyWithPendingCounts();
        
        // Include the view
        ob_start();
        include __DIR__ . '/../views/dean/dean_manage_faculty_profiles.php';
        echo ob_get_clean();
    }
}
