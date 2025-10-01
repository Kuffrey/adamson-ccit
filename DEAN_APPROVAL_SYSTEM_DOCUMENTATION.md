# Dean Approval System for Faculty Certifications and Research

## Overview

This system implements a comprehensive dean approval workflow for faculty certifications and research submissions in the adamson-ccit project. The dean can review, approve, or reject submissions from faculty members for both professional certifications and academic research.

## Database Structure

### Faculty Submissions Table
The `faculty_submissions` table serves as the central approval queue:

```sql
CREATE TABLE faculty_submissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    faculty_id INT NOT NULL,
    submission_type ENUM('research', 'certification', 'news') NOT NULL,
    title VARCHAR(500) NOT NULL,
    description TEXT,
    content LONGTEXT,
    category VARCHAR(100),
    related_item_id INT NULL COMMENT 'Links to the original research/certification record',
    status ENUM('draft','submitted','under_review','approved','rejected','published') DEFAULT 'submitted',
    submitted_at TIMESTAMP NULL,
    reviewed_at TIMESTAMP NULL,
    reviewed_by INT NULL,
    review_notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

### Integration with Existing Tables
- Links to `certifications` table via `related_item_id` for certification submissions
- Links to `research` table via `related_item_id` for research submissions  
- Links to `users` table for faculty and reviewer information

## Key Features

### 1. Faculty Submission Flow

#### Certifications
- Faculty adds a certification through the existing certification management interface
- System automatically creates a corresponding `faculty_submissions` record
- Dean receives notification of pending certification approval

#### Research
- Faculty creates research as a draft
- Faculty clicks "Submit for Review" 
- System creates a `faculty_submissions` record linked to the research
- Research status changes to 'review' pending dean approval

#### News & Content
- Faculty creates news articles, announcements, or other content
- Content is automatically submitted to dean approval queue
- Dean can approve, reject, or directly publish content

### 2. Dean Approval Interfaces

#### Multiple Specialized Dean Views:
1. **General Approvals Dashboard** (`dean_approvals.php`) - Overview with quick access cards
2. **Certification Approvals** (`dean_certifications.php`) - Dedicated certification interface with tabs
3. **Research Approvals** (`dean_research_approvals.php`) - Research-specific interface with abstract preview
4. **News & Content Approvals** (`dean_news_approvals.php`) - News content with publish options
5. **Submission Queue** (`dean_pending_submissions.php`) - Comprehensive oversight tool

#### Approval Actions:
- **Approve**: Updates both submission status and related item status to 'approved'
- **Reject**: Updates both submission status and related item status to 'rejected'
- **Publish** (News only): Directly publishes content and marks as 'published'
- **Review Notes**: Optional comments from dean for feedback

### 3. Enhanced Navigation
- Organized sidebar with approval counts by type
- Quick action cards for each content type
- Badge indicators showing pending counts
- Streamlined workflow for dean efficiency

## Files Modified/Created

### Models
- **`app/models/FacultySubmissions.php`** - Central submission management
  - `create()` - Create new submission
  - `createFromCertification()` - Auto-create from certification
  - `createFromResearch()` - Auto-create from research
  - `updateStatus()` - Approve/reject with cascading updates
  - `getAllSubmissionsWithFacultyDetails()` - Get all with faculty info
  - `getPendingSubmissions()` - Get pending approvals
  - `getSubmissionsByType()` - Filter by submission type

### Views
- **`app/views/dean/dean_certifications.php`** - Dedicated certification approval interface
- **`app/views/dean/dean_research_approvals.php`** - Research-specific approval interface
- **`app/views/dean/dean_news_approvals.php`** - News and content approval interface
- **`app/views/dean_approvals.php`** - Enhanced general approval dashboard
- **`app/views/dean/dean_pending_submissions.php`** - Updated submission queue
- **`app/views/faculty_manage_certifications.php`** - Auto-submit certifications
- **`app/views/faculty_manage_research.php`** - Auto-submit research

### Controllers & Routing
- **`app/controllers/Router.php`** - Added routes for:
  - `dean_certifications` - Certification approval interface
  - `dean_research_approvals` - Research approval interface  
  - `dean_news_approvals` - News approval interface
- **`app/views/dean/_dean_sidebar.php`** - Enhanced sidebar with organized approval sections

### Database Updates
- Added `related_item_id` column to `faculty_submissions` table
- Added index for performance: `idx_related_item_id`

## Workflow Diagram

```
Faculty Side:
[Create Certification] → [Auto-Submit to Dean] → [Pending Status]
[Create Research] → [Submit for Review] → [Pending Status]

Dean Side:
[View Pending Submissions] → [Review Details] → [Approve/Reject] → [Update Status]
                                                                        ↓
                                [Cascading Update to Original Record]
```

## User Roles and Permissions

### Faculty
- Can create certifications and research
- Can submit research for dean review
- Cannot approve their own submissions
- Can view their submission status

### Dean  
- Can view all faculty submissions
- Can approve or reject submissions
- Can add review notes
- Can filter by submission type
- Has oversight of all faculty content

## Testing

### Test Data Created
1. Test faculty user: username `faculty` (John Faculty)
2. Test dean user: username `dean` (Dean Administrator)  
3. Sample certification: "AWS Cloud Practitioner"
4. Sample research: "Machine Learning in Education"
5. Corresponding faculty_submissions records

### Test URLs
- General Approvals Dashboard: `/public/index.php?page=dean_approvals`
- Certification Approvals: `/public/index.php?page=dean_certifications`
- Research Approvals: `/public/index.php?page=dean_research_approvals`
- News & Content Approvals: `/public/index.php?page=dean_news_approvals`
- Submission Queue: `/public/index.php?page=dean_pending_submissions`

### Test Script
Run `/test_dean_approval_system.php` to verify system functionality.

## Security Considerations

1. **Authentication**: All dean pages require authentication and dean role
2. **Authorization**: Faculty can only submit their own items
3. **Input Validation**: All form inputs are escaped and validated
4. **Database Integrity**: Foreign key constraints maintain data consistency
5. **Transaction Safety**: Critical operations use database transactions

## Future Enhancements

1. **Email Notifications**: Notify faculty of approval/rejection decisions
2. **Bulk Actions**: Allow dean to approve/reject multiple submissions at once
3. **Advanced Filtering**: Filter by date range, department, status combinations
4. **Approval History**: Track all approval actions and changes
5. **Dashboard Metrics**: Statistics on approval times and patterns

## Troubleshooting

### Common Issues:
1. **Missing related_item_id**: Ensure the column exists in faculty_submissions table
2. **Permission errors**: Verify user roles are correctly set in users table
3. **Missing faculty names**: Ensure users have first_name and last_name populated
4. **Department display issues**: Verify departments table exists and has data

### Database Verification:
```sql
-- Check submission records
SELECT fs.*, u.username, u.first_name, u.last_name 
FROM faculty_submissions fs 
LEFT JOIN users u ON fs.faculty_id = u.id;

-- Check certification submissions
SELECT * FROM faculty_submissions WHERE submission_type = 'certification';

-- Check research submissions  
SELECT * FROM faculty_submissions WHERE submission_type = 'research';
```

## Conclusion

The dean approval system provides a comprehensive workflow for managing faculty submissions while maintaining proper oversight and approval processes. The system is integrated with existing certification and research management features, providing a seamless experience for both faculty and administrative users.