<?php
require_once __DIR__ . '/../lib/Auth.php';

class Router {
    public static function route(): void
    {
        // Session must be started in public/index.php (before header.php)
        $base = '/adamson-ccit/public/index.php?page=';
        $page = $_GET['page'] ?? 'home';

        switch ($page) {

            /* -------------------- PUBLIC: ABOUT -------------------- */
            case 'about_history':
                include __DIR__ . '/../views/about_history.php'; break;

            case 'about_vision_mission':
                include __DIR__ . '/../views/about_vision_mission.php'; break;

            // Alias to keep old links working
            case 'about_mission_vision':
                if (!headers_sent()) {
                    header('Location: ' . $base . 'about_vision_mission', true, 302);
                    exit;
                }
                echo '<script>location.href="'.htmlspecialchars($base.'about_vision_mission').'";</script>';
                exit;

            case 'about_industry_partners':
                include __DIR__ . '/../views/about_industry_partners.php'; break;

            /* -------------------- PUBLIC: NEWS, EVENTS, ANNOUNCEMENTS -------------------- */
            case 'news':
                include __DIR__ . '/../views/news.php'; break;

            case 'events':
                include __DIR__ . '/../views/events.php'; break;

            // NEW: public announcements list + single view
            case 'announcements':
                require_once __DIR__ . '/HomeController.php';
                $hc = new HomeController();
                echo $hc->announcements(); break;

            case 'announcement_view':
                require_once __DIR__ . '/HomeController.php';
                $hc = new HomeController();
                echo $hc->announcementView(); break;

            /* -------------------- PUBLIC: ADMISSIONS -------------------- */
            case 'admission_freshman':
                include __DIR__ . '/../views/admission_freshman.php'; break;

            case 'admission_transferee': // spelling OK
                include __DIR__ . '/../views/admission_transferee.php'; break;

            case 'admission_graduate_school':
                include __DIR__ . '/../views/admission_graduate_school_and_juris_doctor.php'; break;

            /* -------------------- PUBLIC: PROGRAMS -------------------- */
            case 'programs_undergraduate':
                include __DIR__ . '/../views/programs_undergraduate.php'; break;

            case 'programs_dual_degree':
                include __DIR__ . '/../views/programs_dual_degree.php'; break;

            case 'programs_graduate_studies':
                include __DIR__ . '/../views/programs_graduate_studies.php'; break;

            /* -------------------- PUBLIC: STUDENTS -------------------- */
            case 'student_organizations':
                include __DIR__ . '/../views/student_organizations.php'; break;

            case 'student_scholarships':
                include __DIR__ . '/../views/student_scholarships.php'; break;

            case 'student_research':
                include __DIR__ . '/../views/student_research.php'; break;

            case 'student_certifications':
                include __DIR__ . '/../views/student_certifications.php'; break;

            case 'student_testimonials':
                include __DIR__ . '/../views/student_testimonials.php'; break;

            /* -------------------- PUBLIC: FACULTY -------------------- */
            case 'faculty_profile':
                include __DIR__ . '/../views/faculty_profile.php'; break;

            case 'faculty_research':
                include __DIR__ . '/../views/faculty_research.php'; break;

            case 'faculty_certifications':
                include __DIR__ . '/../views/faculty_certifications.php'; break;

            /* -------------------- PUBLIC TOOLS -------------------- */
            case 'career_pathway_generator':
                include __DIR__ . '/../views/career_pathway_generator.php'; break;

            /* -------------------- AUTH VIEWS -------------------- */
            case 'login_guest_student':
                include __DIR__ . '/../views/login_guest_student.php'; break;

            case 'login_faculty':
                include __DIR__ . '/../views/login_faculty.php'; break;

            case 'login_admin':
                include __DIR__ . '/../views/login_admin.php'; break;

            /* -------------------- STUDENT FEATURES (public site, gated) -------------------- */
            case 'student_profile':
                Auth::requireRole(['student'], $base . 'login_guest_student');
                include __DIR__ . '/../views/student_profile.php'; break;

            case 'student_settings':
                Auth::requireRole(['student'], $base . 'login_guest_student');
                include __DIR__ . '/../views/student_settings.php'; break;

            case 'student_career_results':
                // Viewable for guests (save actions should re-check in view)
                include __DIR__ . '/../views/student_career_results.php'; break;

            /* -------------------- FACULTY CMS -------------------- */
            case 'faculty_dashboard':
                Auth::requireRole(['faculty', 'admin', 'dean'], $base . 'login_faculty');
                require_once __DIR__ . '/FacultyController.php';
                $fc = new FacultyController();
                echo $fc->dashboard(); break;

            case 'faculty_manage_research':
                Auth::requireRole(['faculty', 'admin', 'dean'], $base . 'login_faculty');
                require_once __DIR__ . '/FacultyController.php';
                $fc = new FacultyController();
                echo $fc->manageResearch(); break;

            case 'faculty_manage_certifications':
                Auth::requireRole(['faculty', 'admin', 'dean'], $base . 'login_faculty');
                require_once __DIR__ . '/FacultyController.php';
                $fc = new FacultyController();
                echo $fc->manageCertifications(); break;

            /* -------------------- ADMIN + DEAN CMS -------------------- */
            case 'admin_dashboard':
                Auth::requireRole(['admin','dean'], $base . 'login_admin');
                require_once __DIR__ . '/AdminController.php';
                $ac = new AdminController();
                echo $ac->dashboard(); break;

            case 'admin_manage_news':
                Auth::requireRole(['admin','dean'], $base . 'login_admin');
                require_once __DIR__ . '/AdminController.php';
                $ac = new AdminController();
                echo $ac->manageNews(); break;

            case 'admin_manage_events':
                require_once __DIR__ . '/AdminController.php';
                $c = new AdminController();
                echo $c->manageEvents();
                break;

            case 'admin_manage_programs':
                // If "programs" includes managing Events or static pages, Dean can help
                Auth::requireRole(['admin','dean'], $base . 'login_admin');
                require_once __DIR__ . '/AdminController.php';
                $ac = new AdminController();
                echo $ac->managePrograms(); break;

            case 'admin_manage_faculty':
                // Keep user/faculty management Admin-only
                Auth::requireRole(['admin'], $base . 'login_admin');
                require_once __DIR__ . '/AdminController.php';
                $ac = new AdminController();
                echo $ac->manageFaculty(); break;

            // NEW: announcements manager (Admin + Dean can create/publish)
            case 'admin_manage_announcements':
                Auth::requireRole(['admin','dean'], $base . 'login_admin');
                require_once __DIR__ . '/AdminController.php';
                $ac = new AdminController();
                echo $ac->manageAnnouncements(); break;

            /* -------------------- DEAN -------------------- */
            case 'dashboard_dean':
                Auth::requireRole(['dean'], $base . 'login_faculty');
                require_once __DIR__ . '/DeanController.php';
                $dc = new DeanController();
                echo $dc->dashboard(); break;

            case 'dean_approvals':
                Auth::requireRole(['dean'], $base . 'login_faculty');
                require_once __DIR__ . '/DeanController.php';
                $dc = new DeanController();
                echo $dc->approvals(); break;

            // Optional: dean-specific create screens (can also link to admin_manage_* with action=new)
            case 'dean_create_news':
                Auth::requireRole(['dean'], $base . 'login_faculty');
                require_once __DIR__ . '/DeanController.php';
                $dc = new DeanController();
                echo $dc->createNews(); break;

            case 'dean_create_event':
                Auth::requireRole(['dean'], $base . 'login_faculty');
                require_once __DIR__ . '/DeanController.php';
                $dc = new DeanController();
                echo $dc->createEvent(); break;

            case 'dean_create_announcement':
                Auth::requireRole(['dean'], $base . 'login_faculty');
                require_once __DIR__ . '/DeanController.php';
                $dc = new DeanController();
                echo $dc->createAnnouncement(); break;

            /* -------------------- LOGOUT -------------------- */
            case 'logout':
                Auth::logout();
                if (!headers_sent()) {
                    header('Location: /adamson-ccit/public/index.php', true, 303);
                    exit;
                }
                echo '<script>location.href="/adamson-ccit/public/index.php";</script>';
                exit;

            /* -------------------- HOME (default) -------------------- */
            case 'home':
            default:
                require_once __DIR__ . '/HomeController.php';
                $hc = new HomeController();
                echo $hc->index(); break;
        }
    }
}
