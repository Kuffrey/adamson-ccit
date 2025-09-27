<?php
require_once __DIR__ . '/../lib/Auth.php';

class Router {
    public static function route(): void
    {
        $base = '/adamson-ccit/public/index.php?page=';
        $page = $_GET['page'] ?? 'home';

        switch ($page) {
            /* -------------------- ADMIN HOMEPAGE CMS -------------------- */
            case 'admin_homepage':
                Auth::requireRole(['admin','dean'], $base . 'login_admin');
                require_once __DIR__ . '/AdminHomepageController.php';
                echo (new AdminHomepageController())->index();
                break;

            case 'admin_homepage_save':
                Auth::requireRole(['admin','dean'], $base . 'login_admin');
                require_once __DIR__ . '/AdminHomepageController.php';
                (new AdminHomepageController())->save();
                break;

            // Legacy alias used by your existing sidebar: ?page=admin_manage_homepage
            case 'admin_manage_homepage':
                Auth::requireRole(['admin','dean'], $base . 'login_admin');
                require_once __DIR__ . '/AdminHomepageController.php';
                $c = new AdminHomepageController();
                if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') { $c->save(); }
                else { echo $c->index(); }
                break;

            /* -------------------- QUICK ACTIONS MANAGEMENT -------------------- */
            case 'admin_manage_quick_actions':
                Auth::requireRole(['admin','dean'], $base . 'login_admin');
                require_once __DIR__ . '/QuickActionsController.php';
                $c = new QuickActionsController();
                if (($_POST['action'] ?? '') === 'save' || ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') { 
                    echo $c->save(); 
                } else { 
                    echo $c->index(); 
                }
                break;

            /* -------------------- PUBLIC: ABOUT -------------------- */
            case 'about_history':
                include __DIR__ . '/../views/about_history.php'; break;

            case 'about_vision_mission':
                include __DIR__ . '/../views/about_vision_mission.php'; break;

            case 'about_mission_vision': // alias
                if (!headers_sent()) { header('Location: ' . $base . 'about_vision_mission', true, 302); exit; }
                echo '<script>location.href='.json_encode($base.'about_vision_mission').';</script>'; exit;

            case 'about_industry_partners':
                include __DIR__ . '/../views/about_industry_partners.php'; break;

            /* -------------------- PUBLIC: NEWS/EVENTS/ANNOUNCEMENTS -------------------- */
            case 'news':
                include __DIR__ . '/../views/news.php'; break;

            case 'news_article':
                include __DIR__ . '/../views/news_article.php'; break;

            case 'events':
                include __DIR__ . '/../views/events.php'; break;

            case 'announcements':
                require_once __DIR__ . '/HomeController.php';
                echo (new HomeController())->announcements(); break;

            case 'announcement_view':
                require_once __DIR__ . '/HomeController.php';
                echo (new HomeController())->announcementView(); break;

            /* -------------------- PUBLIC: ADMISSIONS -------------------- */
            case 'admission_freshman':
                include __DIR__ . '/../views/admission_freshman.php'; break;

            case 'admission_transferee':
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

            /* -------------------- PUBLIC TOOL -------------------- */
            case 'career_pathway_generator':
                include __DIR__ . '/../views/career_pathway_generator.php'; break;

            case 'virtual_tour':
                include __DIR__ . '/../views/virtual_tour.php'; break;

            /* -------------------- AUTH VIEWS -------------------- */
            case 'login_guest_student':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    require_once __DIR__ . '/StudentLoginController.php';
                    $error = StudentLoginController::handle();
                }
                include __DIR__ . '/../views/login_guest_student.php'; break;

            case 'login_faculty':
                include __DIR__ . '/../views/login_faculty.php'; break;

            case 'login_admin':
                include __DIR__ . '/../views/login_admin.php'; break;

            case 'login_dean':
                include __DIR__ . '/../views/login_dean.php'; break;

            /* -------------------- STUDENT (gated) -------------------- */
            case 'student_profile':
                Auth::requireRole(['student'], $base . 'login_guest_student');
                include __DIR__ . '/../views/student_profile.php'; break;

            case 'student_settings':
                Auth::requireRole(['student'], $base . 'login_guest_student');
                include __DIR__ . '/../views/student_settings.php'; break;

            case 'student_career_results':
                include __DIR__ . '/../views/student_career_results.php'; break;

            /* -------------------- FACULTY CMS -------------------- */
            case 'faculty_dashboard':
                Auth::requireRole(['faculty','admin','dean'], $base . 'login_faculty');
                require_once __DIR__ . '/FacultyController.php';
                echo (new FacultyController())->dashboard(); break;

            case 'faculty_manage_research':
                Auth::requireRole(['faculty','admin','dean'], $base . 'login_faculty');
                require_once __DIR__ . '/FacultyController.php';
                echo (new FacultyController())->manageResearch(); break;

            case 'faculty_manage_certifications':
                Auth::requireRole(['faculty','admin','dean'], $base . 'login_faculty');
                require_once __DIR__ . '/FacultyController.php';
                echo (new FacultyController())->manageCertifications(); break;

            case 'faculty_portfolio':
                Auth::requireRole(['faculty','admin','dean'], $base . 'login_faculty');
                require_once __DIR__ . '/FacultyController.php';
                echo (new FacultyController())->portfolio(); break;

            /* -------------------- ADMIN + DEAN CMS -------------------- */
            case 'admin_dashboard':
                Auth::requireRole(['admin','dean'], $base . 'login_admin');
                require_once __DIR__ . '/AdminController.php';
                echo (new AdminController())->dashboard(); break;

            case 'admin_manage_news':
                Auth::requireRole(['admin','dean'], $base . 'login_admin');
                require_once __DIR__ . '/AdminController.php';
                echo (new AdminController())->manageNews(); break;

            case 'admin_news_settings':
                Auth::requireRole(['admin','dean'], $base . 'login_admin');
                require_once __DIR__ . '/AdminNewsPageController.php';
                AdminNewsPageController::handle(); break;

            case 'admin_manage_programs':
                Auth::requireRole(['admin','dean'], $base . 'login_admin');
                include __DIR__ . '/../views/admin_manage_programs.php'; break;

            case 'admin_programs_undergraduate':
                Auth::requireRole(['admin','dean'], $base . 'login_admin');
                include __DIR__ . '/../views/admin/admin_programs_undergraduate.php'; break;

            case 'admin_programs_graduate':
                Auth::requireRole(['admin','dean'], $base . 'login_admin');
                include __DIR__ . '/../views/admin/admin_programs_graduate.php'; break;
                
            case 'admin_debug':
                Auth::requireRole(['admin'], $base . 'login_admin');
                include __DIR__ . '/../views/admin/debug.php'; break;

            case 'admin_student_organizations':
                Auth::requireRole(['admin','dean'], $base . 'login_admin');
                require_once __DIR__ . '/AdminController.php';
                echo (new AdminController())->studentOrganizations(); break;

            case 'admin_student_scholarships':
                Auth::requireRole(['admin','dean'], $base . 'login_admin');
                require_once __DIR__ . '/AdminController.php';
                echo (new AdminController())->studentScholarships(); break;

            case 'admin_student_research':
                Auth::requireRole(['admin','dean'], $base . 'login_admin');
                require_once __DIR__ . '/AdminController.php';
                echo (new AdminController())->studentResearch(); break;

            case 'admin_student_certifications':
                Auth::requireRole(['admin','dean'], $base . 'login_admin');
                require_once __DIR__ . '/AdminController.php';
                echo (new AdminController())->studentCertifications(); break;

            case 'admin_student_testimonials':
                Auth::requireRole(['admin','dean'], $base . 'login_admin');
                require_once __DIR__ . '/AdminController.php';
                echo (new AdminController())->studentTestimonials(); break;

            case 'admin_faculty_profile':
                Auth::requireRole(['admin','dean'], $base . 'login_admin');
                include __DIR__ . '/../views/admin/admin_faculty_profile.php'; break;

            case 'admin_faculty_research':
                Auth::requireRole(['admin','dean'], $base . 'login_admin');
                include __DIR__ . '/../views/admin/admin_faculty_research.php'; break;

            case 'admin_faculty_certifications':
                Auth::requireRole(['admin','dean'], $base . 'login_admin');
                include __DIR__ . '/../views/admin/admin_faculty_certifications.php'; break;

            case 'admin_manage_faculty':
                Auth::requireRole(['admin'], $base . 'login_admin');
                require_once __DIR__ . '/AdminController.php';
                echo (new AdminController())->manageFaculty(); break;

            case 'admin_manage_announcements':
                Auth::requireRole(['admin','dean'], $base . 'login_admin');
                require_once __DIR__ . '/AdminController.php';
                echo (new AdminController())->manageAnnouncements(); break;

            case 'admin_manage_about':
                Auth::requireRole(['admin','dean'], $base . 'login_admin');
                require_once __DIR__ . '/AdminController.php';
                echo (new AdminController())->manageAbout(); break;

            case 'admin_about_vision_mission':
                Auth::requireRole(['admin','dean'], $base . 'login_admin');
                require_once __DIR__ . '/../views/admin/about_vision_mission_form.php'; break;

            case 'admin_about_vision_mission_save':
                Auth::requireRole(['admin','dean'], $base . 'login_admin');
                require_once __DIR__ . '/../models/AboutVisionMission.php';
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $m  = new AboutVisionMission();
                    $ok = $m->update($_POST);
                    $to = $base . 'admin_about_vision_mission' . ($ok ? '&success=1' : '&success=0');
                    if (!headers_sent()) { header('Location: ' . $to, true, 303); exit; }
                    echo '<script>location.href='.json_encode($to).';</script>'; exit;
                }
                if (!headers_sent()) { header('Location: ' . $base . 'admin_about_vision_mission', true, 303); exit; }
                echo '<script>location.href='.json_encode($base.'admin_about_vision_mission').';</script>'; exit;

            case 'admin_manage_events':
                Auth::requireRole(['admin','dean'], $base . 'login_admin');
                require_once __DIR__ . '/AdminController.php';
                echo (new AdminController())->manageEvents(); break;

            /* -------------------- ADMISSION CMS -------------------- */
            case 'admin_admission_freshman':
                Auth::requireRole(['admin','dean'], $base . 'login_admin');
                require_once __DIR__ . '/AdminAdmissionFreshmanController.php';
                AdminAdmissionFreshmanController::handle(); break;

            case 'admin_admission_transferee':
                Auth::requireRole(['admin','dean'], $base . 'login_admin');
                require_once __DIR__ . '/AdminAdmissionTransfereeController.php';
                AdminAdmissionTransfereeController::handle(); break;

            case 'admin_admission_graduate':
                Auth::requireRole(['admin','dean'], $base . 'login_admin');
                require_once __DIR__ . '/AdminAdmissionGraduateController.php';
                AdminAdmissionGraduateController::handle(); break;

            /* -------------------- DEAN -------------------- */
            case 'dean_dashboard':
                Auth::requireRole(['dean'], $base . 'login_faculty');
                require_once __DIR__ . '/DeanController.php';
                echo (new DeanController())->dashboard(); break;

            case 'dean_approvals':
                Auth::requireRole(['dean'], $base . 'login_faculty');
                require_once __DIR__ . '/DeanController.php';
                echo (new DeanController())->approvals(); break;

            case 'dean_create_news':
                Auth::requireRole(['dean'], $base . 'login_faculty');
                require_once __DIR__ . '/DeanController.php';
                echo (new DeanController())->createNews(); break;

            case 'dean_create_event':
                Auth::requireRole(['dean'], $base . 'login_faculty');
                require_once __DIR__ . '/DeanController.php';
                echo (new DeanController())->createEvent(); break;

            case 'dean_create_announcement':
                Auth::requireRole(['dean'], $base . 'login_faculty');
                require_once __DIR__ . '/DeanController.php';
                echo (new DeanController())->createAnnouncement(); break;

            /* -------------------- STUDENT CERT ACTIONS -------------------- */
            case 'student_cert_save':
                Auth::requireRole(['student'], $base . 'login_guest_student');
                require_once __DIR__ . '/StudentCertController.php';
                (new StudentCertController())->save(); break;

            case 'student_cert_delete':
                Auth::requireRole(['student'], $base . 'login_guest_student');
                require_once __DIR__ . '/StudentCertController.php';
                (new StudentCertController())->delete(); break;

            /* -------------------- LOGOUT -------------------- */
            case 'logout':
                Auth::logout();
                if (!headers_sent()) { header('Location: /adamson-ccit/public/index.php', true, 303); exit; }
                echo '<script>location.href="/adamson-ccit/public/index.php";</script>'; exit;

            /* -------------------- HOME (default) -------------------- */
            case 'home':
            default:
                require_once __DIR__ . '/HomeController.php';
                echo (new HomeController())->index(); break;

                // Legacy alias: ?page=admin_manage_homepage  -> uses the new controller
case 'admin_manage_homepage':
    Auth::requireRole(['admin','dean'], $base . 'login_admin');
    require_once __DIR__ . '/AdminHomepageController.php';
    $c = new AdminHomepageController();
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
        $c->save();
    } else {
        echo $c->index();
    }
    break;

        }
    }
}
