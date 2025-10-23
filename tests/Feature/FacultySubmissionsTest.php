<?php

use PHPUnit\Framework\TestCase;

// Only check the actual location
$facultySubmissionsPath = __DIR__ . '/../../app/models/FacultySubmissions.php';

if (file_exists($facultySubmissionsPath)) {
    require_once $facultySubmissionsPath;
}

final class FacultySubmissionsTest extends TestCase
{
    private $facultyId = 1; // Use a valid test faculty ID

    public function setUp(): void
    {
        parent::setUp();
        global $facultySubmissionsPath;
        if (!class_exists('FacultySubmissions')) {
            $this->markTestSkipped('FacultySubmissions.php not found at: ' . $facultySubmissionsPath);
        }
    }

    public function testCreateNewsSubmission()
    {
        // Checks if a faculty can successfully create a news submission.
        // Verifies that the returned submission ID is valid and deletes the test record.

        $data = [
            'faculty_id'      => $this->facultyId,
            'submission_type' => 'news',
            'title'           => 'Test News Title',
            'description'     => 'Test news description',
            'content'         => 'Test news content for submission.',
            'category'        => 'news',
            'status'          => 'submitted'
        ];
        $submissionId = FacultySubmissions::create($data);
        // Accept string or int, but must be numeric and > 0
        $this->assertTrue(is_numeric($submissionId));
        $this->assertGreaterThan(0, (int)$submissionId);

        // Clean up
        FacultySubmissions::delete($submissionId);
    }

    public function testCreateSubmissionTitleMaxLength()
    {
        // Checks validation: tries to create a submission with a title longer than 100 characters.
        // Expects an exception to be thrown (title must not exceed 100 characters).

        $longTitle = str_repeat('A', 101);
        $data = [
            'faculty_id'      => $this->facultyId,
            'submission_type' => 'news',
            'title'           => $longTitle,
            'description'     => 'desc',
            'content'         => 'content',
            'category'        => 'news',
            'status'          => 'submitted'
        ];
        $this->expectException(Exception::class);
        FacultySubmissions::create($data);
    }

    public function testEditNewsSubmission()
    {
        // Checks editing an existing news submission.
        // Verifies that the title and category are updated correctly.

        // Create first
        $data = [
            'faculty_id'      => $this->facultyId,
            'submission_type' => 'news',
            'title'           => 'Edit Test Title',
            'description'     => 'Edit test description',
            'content'         => 'Edit test content.',
            'category'        => 'news',
            'status'          => 'submitted'
        ];
        $submissionId = FacultySubmissions::create($data);

        // Edit
        $pdo = new PDO("mysql:host=localhost;dbname=adamson_ccit", "root", "");
        $stmt = $pdo->prepare("UPDATE faculty_submissions SET title = ?, description = ?, content = ?, category = ? WHERE id = ?");
        $result = $stmt->execute([
            'Edited Title',
            'Edited Description',
            'Edited Content',
            'research',
            $submissionId
        ]);
        $this->assertTrue($result);

        // Verify
        $updated = FacultySubmissions::getById($submissionId);
        $this->assertEquals('Edited Title', $updated['title']);
        $this->assertEquals('research', $updated['category']);

        // Clean up
        FacultySubmissions::delete($submissionId);
    }

    public function testDeleteNewsSubmission()
    {
        // Checks deleting a news submission.
        // Verifies that the submission no longer exists after deletion.

        $data = [
            'faculty_id'      => $this->facultyId,
            'submission_type' => 'news',
            'title'           => 'Delete Test Title',
            'description'     => 'Delete test description',
            'content'         => 'Delete test content.',
            'category'        => 'news',
            'status'          => 'submitted'
        ];
        $submissionId = FacultySubmissions::create($data);

        $deleted = FacultySubmissions::delete($submissionId);
        $this->assertTrue($deleted);

        $deletedSubmission = FacultySubmissions::getById($submissionId);
        $this->assertFalse($deletedSubmission);
    }

    public function testViewSubmissionDetails()
    {
        // Checks viewing the details of a specific submission using getById.
        // Verifies that the returned data matches what was submitted.

        $data = [
            'faculty_id'      => $this->facultyId,
            'submission_type' => 'news',
            'title'           => 'View Details Test',
            'description'     => 'desc',
            'content'         => 'content for view details',
            'category'        => 'news',
            'status'          => 'submitted'
        ];
        $submissionId = FacultySubmissions::create($data);

        $submission = FacultySubmissions::getById($submissionId);
        $this->assertIsArray($submission);
        $this->assertEquals($submissionId, $submission['id']);
        $this->assertEquals('View Details Test', $submission['title']);
        $this->assertEquals('content for view details', $submission['content']);
        $this->assertEquals('news', $submission['category']);
        $this->assertEquals('submitted', $submission['status']);

        FacultySubmissions::delete($submissionId);
    }

    public function testCreateSubmissionMissingTitle()
    {
        // Checks validation: tries to create a submission without a title.
        // Expects an exception to be thrown (title is required).

        $data = [
            'faculty_id'      => $this->facultyId,
            'submission_type' => 'news',
            'description'     => 'desc',
            'content'         => 'content',
            'category'        => 'news',
            'status'          => 'submitted'
        ];
        $this->expectException(Exception::class);
        FacultySubmissions::create($data);
    }

    public function testCreateSubmissionMissingContent()
    {
        // Checks validation: tries to create a submission without content.
        // Expects an exception to be thrown (content is required).

        $data = [
            'faculty_id'      => $this->facultyId,
            'submission_type' => 'news',
            'title'           => 'Title',
            'category'        => 'news',
            'status'          => 'submitted'
        ];
        $this->expectException(Exception::class);
        FacultySubmissions::create($data);
    }

    public function testStatusTransitions()
    {
        // Checks status changes: submitted → under_review → approved.
        // Verifies that the status updates correctly after each transition.

        $data = [
            'faculty_id'      => $this->facultyId,
            'submission_type' => 'news',
            'title'           => 'Status Transition',
            'description'     => 'desc',
            'content'         => 'content',
            'category'        => 'news',
            'status'          => 'submitted'
        ];
        $submissionId = FacultySubmissions::create($data);

        // Move to under_review
        $result = FacultySubmissions::updateStatus($submissionId, 'under_review', 2, 'Dean reviewing.');
        $this->assertTrue($result);
        $updated = FacultySubmissions::getById($submissionId);
        $this->assertEquals('under_review', $updated['status']);

        // Move to approved
        $result = FacultySubmissions::updateStatus($submissionId, 'approved', 2, 'Dean approved.');
        $this->assertTrue($result);
        $updated = FacultySubmissions::getById($submissionId);
        $this->assertEquals('approved', $updated['status']);
        FacultySubmissions::delete($submissionId);
    }
}
