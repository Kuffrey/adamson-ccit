<?php
require_once 'app/models/Announcement.php';

echo "=== ADDING SAMPLE ANNOUNCEMENTS ===\n\n";

$sampleAnnouncements = [
    [
        'title' => 'CCIT Enrollment Period Extended',
        'excerpt' => 'Due to high demand, we are extending the enrollment period for all CCIT programs.',
        'content' => 'We are pleased to announce that the enrollment period for all College of Computer and Information Technology programs has been extended until December 15, 2025. This extension allows more students to join our innovative programs in Computer Science, Information Technology, and Information Systems. For more information about enrollment requirements and procedures, please visit our admissions office or contact our enrollment hotline.',
        'category' => 'deadline',
        'status' => 'published',
        'date' => '2025-10-15'
    ],
    [
        'title' => 'New Scholarship Program Available',
        'excerpt' => 'CCIT launches new merit-based scholarship program for outstanding students.',
        'content' => 'The College of Computer and Information Technology is proud to announce the launch of our new Merit Excellence Scholarship Program. This program aims to support outstanding students who demonstrate exceptional academic performance and leadership potential. Eligible students can receive up to 75% tuition discount. Applications are now open and will close on November 30, 2025. Requirements include a minimum GPA of 3.5, letters of recommendation, and a personal statement.',
        'category' => 'general',
        'status' => 'published',
        'date' => '2025-10-12'
    ],
    [
        'title' => 'Campus WiFi Maintenance Schedule',
        'excerpt' => 'Scheduled maintenance will temporarily affect campus WiFi connectivity.',
        'content' => 'Please be advised that scheduled maintenance of our campus WiFi infrastructure will take place on October 20-21, 2025, from 2:00 AM to 6:00 AM each day. During this period, internet connectivity may be intermittent or unavailable in certain areas of the campus. We apologize for any inconvenience and recommend that students and faculty plan accordingly. Emergency internet access will be available at the library information desk.',
        'category' => 'advisory',
        'status' => 'published',
        'date' => '2025-10-18'
    ],
    [
        'title' => 'Student Research Symposium 2025',
        'excerpt' => 'Annual research symposium showcasing outstanding student projects and innovations.',
        'content' => 'The CCIT Student Research Symposium 2025 will be held on November 25, 2025, at the university auditorium. This premier event showcases the innovative research projects and technological solutions developed by our students throughout the academic year. Featured presentations will include AI applications, cybersecurity solutions, mobile app development, and data analytics projects. Registration for presenters is open until November 1, 2025. Awards will be given for the most outstanding projects in each category.',
        'category' => 'general',
        'status' => 'published',
        'date' => '2025-11-01'
    ],
    [
        'title' => 'Library System Upgrade',
        'excerpt' => 'CCIT library introduces new digital catalog and e-resource access system.',
        'content' => 'We are excited to announce the successful upgrade of our library management system. The new system provides enhanced search capabilities, improved e-book access, and streamlined reservation processes. Students can now access over 10,000 digital resources, including technical journals, research databases, and programming reference materials. Training sessions for the new system will be conducted every Tuesday and Thursday at 3:00 PM in the library conference room. All students are encouraged to attend.',
        'category' => 'general',
        'status' => 'published',
        'date' => '2025-10-08'
    ]
];

foreach ($sampleAnnouncements as $index => $announcement) {
    echo "Creating announcement " . ($index + 1) . ": {$announcement['title']}\n";
    
    try {
        $id = Announcement::create(
            $announcement['title'],
            $announcement['content'],
            $announcement['status'],
            $announcement['category'],
            $announcement['date'],
            null, // no image
            $announcement['excerpt']
        );
        echo "  Created with ID: $id\n";
    } catch (Exception $e) {
        echo "  Error: " . $e->getMessage() . "\n";
    }
}

// Test the final result
echo "\nTesting final announcements display:\n";
$searchResult = Announcement::searchPublished(
    ['cat' => null, 'year' => null, 'q' => null],
    ['page' => 1, 'perPage' => 10]
);

echo "Total published announcements: " . ($searchResult['total'] ?? 0) . "\n";
echo "Items on page 1: " . count($searchResult['items'] ?? []) . "\n";

if (!empty($searchResult['items'])) {
    echo "\nPublished announcements:\n";
    foreach ($searchResult['items'] as $item) {
        echo "- ID {$item['id']}: {$item['title']} ({$item['category']})\n";
    }
}

echo "\n=== SAMPLE DATA CREATION COMPLETE ===\n";
?>