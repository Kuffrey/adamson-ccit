<?php
declare(strict_types=1);

class Submission {
    public static function pendingForAdmin(int $limit = 5): array {
        // demo data; replace with DB later
        return [
            [
                'id' => 101, 'type' => 'news', 'title' => 'AI Lab ribbon cutting',
                'submitted_by' => 'j.doe@adamson.edu.ph', 'created_at' => '2025-08-15 10:22'
            ],
            [
                'id' => 102, 'type' => 'announcement', 'title' => 'Enrollment advisory',
                'submitted_by' => 'm.santos@adamson.edu.ph', 'created_at' => '2025-08-16 09:05'
            ],
        ];
    }
}
