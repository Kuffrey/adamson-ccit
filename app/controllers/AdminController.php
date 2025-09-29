<?php declare(strict_types=1);

// Include debug helper if it exists
$debugHelper = __DIR__ . '/../lib/debug_helper.php';
if (file_exists($debugHelper)) {
    include_once $debugHelper;
}

class AdminController
{
    /** Ensure session exists */
    protected function startSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /** Strict admin-only guard */
    protected function requireAdmin(): void
    {
        $this->startSession();
        $role = $_SESSION['user']['role'] ?? '';
        if ($role !== 'admin') $this->redirect('?page=login');
    }

    /** Allow either Admin or Dean */
    protected function requireAdminOrDean(): void
    {
        $this->startSession();
        $role = $_SESSION['user']['role'] ?? '';
        if (!in_array($role, ['admin','dean'], true)) $this->redirect('?page=login');
    }

    /** Safe redirect w/ JS fallback if headers already sent */
    protected function redirect(string $dest): void
    {
        if (!headers_sent()) { header('Location: ' . $dest); exit; }
        echo '<script>location.href=' . json_encode($dest) . ';</script>'; exit;
    }

    /** ==================== DASHBOARD ==================== */
    public function dashboard(): string
    {
        $this->requireAdminOrDean();

        $core = __DIR__ . '/../core/Model.php'; if (is_file($core)) require_once $core;
        $mNews = __DIR__ . '/../models/News.php'; if (is_file($mNews)) include_once $mNews;
        $mEvent = __DIR__ . '/../models/Event.php'; if (is_file($mEvent)) include_once $mEvent;
        $mAnn  = __DIR__ . '/../models/Announcement.php'; if (is_file($mAnn)) include_once $mAnn;

        $emptyCounts = ['all'=>0,'draft'=>0,'published'=>0,'archived'=>0];
        $newsCounts = (class_exists('News') && method_exists('News','statusCounts')) ? News::statusCounts() : $emptyCounts;
        $eventCounts = (class_exists('Event') && method_exists('Event','statusCounts')) ? Event::statusCounts() : $emptyCounts;
        $announcementCounts = (class_exists('Announcement') && method_exists('Announcement','statusCounts')) ? Announcement::statusCounts() : $emptyCounts;

        $username = $_SESSION['user']['username'] ?? 'Admin';

        ob_start();
        extract(compact('username','newsCounts','eventCounts','announcementCounts'), EXTR_SKIP);
        include __DIR__ . '/../views/admin_dashboard.php';
        return ob_get_clean();
    }

    /** ==================== NEWS ==================== */
    public function manageNews(): string
    {
        $this->requireAdminOrDean();

        $core = __DIR__ . '/../core/Model.php'; if (is_file($core)) require_once $core;
        $newsPath = __DIR__ . '/../models/News.php'; if (is_file($newsPath)) include_once $newsPath;

        $status = isset($_GET['status']) ? strtolower((string)$_GET['status']) : 'all';
        if (!in_array($status, ['all','draft','published','archived'], true)) $status = 'all';

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_news']) && class_exists('News')) {
            $title    = trim($_POST['title']    ?? '');
            $content  = trim($_POST['content']  ?? '');
            $category = trim($_POST['category'] ?? 'news');
            $nStatus  = trim($_POST['status']   ?? 'draft');

            $imageUrl = null;
            if (!empty($_FILES['image']['name']) && ($_FILES['image']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
                $okTypes = ['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp','image/gif'=>'gif'];
                $type    = mime_content_type($_FILES['image']['tmp_name']);
                if (isset($okTypes[$type])) {
                    $ext      = $okTypes[$type];
                    $safeBase = preg_replace('~[^a-zA-Z0-9_-]+~', '-', strtolower(pathinfo($_FILES['image']['name'], PATHINFO_FILENAME)));
                    $fname    = date('Ymd_His').'_'.($safeBase ?: 'news').'.'.$ext;

                    $destDir  = dirname(__DIR__, 2).'/public/uploads/news';
                    if (!is_dir($destDir)) mkdir($destDir, 0777, true);
                    $destPath = $destDir . '/' . $fname;
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $destPath)) {
                        $imageUrl = '/adamson-ccit/public/uploads/news/'.$fname;
                    }
                }
            }
            if ($title !== '' && $content !== '') {
                News::create($title, $content, $nStatus, $category, $imageUrl);
                $status = $nStatus;
            }
            header('Location: ?page=admin_manage_news&status='.urlencode($status)); exit;
        }

        if ($_SERVER['REQUEST_METHOD']=== 'POST' && isset($_POST['update_status'], $_POST['id']) && class_exists('News')) {
            $id      = (int)$_POST['id'];
            $nStatus = trim($_POST['update_status']);
            if ($id > 0) News::updateStatus($id, $nStatus);
            $status = $nStatus;
            header('Location: ?page=admin_manage_news&status='.urlencode($status)); exit;
        }

        if (isset($_GET['delete']) && class_exists('News')) {
            News::delete((int)$_GET['delete']);
            header('Location: ?page=admin_manage_news&status='.urlencode($status)); exit;
        }

        $news   = class_exists('News') ? News::list($status) : [];
        $counts = (class_exists('News') && method_exists('News','statusCounts')) ? News::statusCounts() : ['all'=>0,'draft'=>0,'published'=>0,'archived'=>0];

        ob_start();
        extract(compact('news','status','counts'), EXTR_SKIP);
        include __DIR__ . '/../views/admin_manage_news.php';
        return ob_get_clean();
    }

    /** ==================== ABOUT ==================== */
    public function manageAbout(): string
    {
        $this->requireAdminOrDean();
        require_once __DIR__ . '/../models/AboutHistory.php';
        $model = new AboutHistory();
        $success = false; $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($model->update($_POST)) $success = true;
            else $error = 'Failed to save About page content.';
        }

        $about = $model->get();
        $username = $_SESSION['user']['username'] ?? 'Admin';

        ob_start();
        include __DIR__ . '/../views/admin_manage_about.php';
        return ob_get_clean();
    }

    /** ==================== EVENTS ==================== */
    public function manageEvents(): string
    {
        $this->requireAdminOrDean();

        $core = __DIR__ . '/../core/Model.php'; if (is_file($core)) require_once $core;
        $mdl  = __DIR__ . '/../models/Event.php'; if (is_file($mdl)) include_once $mdl;

        $status = isset($_GET['status']) ? strtolower((string)$_GET['status']) : 'all';
        if (!in_array($status, ['all','draft','published','archived'], true)) $status = 'all';

        $toDT = static function (?string $x): ?string {
            $x = trim((string)$x);
            if ($x === '') return null;
            $x = str_replace('T',' ',$x);
            if (strlen($x) === 16) $x .= ':00';
            return $x;
        };

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_event']) && class_exists('Event')) {
            $title   = trim($_POST['title'] ?? '');
            $desc    = trim($_POST['description'] ?? '');
            $loc     = trim($_POST['location'] ?? '');
            $cat     = trim($_POST['category'] ?? 'career');
            $eStatus = trim($_POST['status'] ?? 'draft');
            $startAt = $toDT($_POST['start_at'] ?? null);
            $endAt   = $toDT($_POST['end_at']   ?? null);

            $imageUrl = null;
            if (!empty($_FILES['image']['name']) && ($_FILES['image']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
                $okTypes = ['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp','image/gif'=>'gif'];
                $type    = mime_content_type($_FILES['image']['tmp_name']);
                if (isset($okTypes[$type])) {
                    $ext      = $okTypes[$type];
                    $safeBase = preg_replace('~[^a-zA-Z0-9_-]+~', '-', strtolower(pathinfo($_FILES['image']['name'], PATHINFO_FILENAME)));
                    $fname    = date('Ymd_His').'_'.($safeBase ?: 'event').'.'.$ext;

                    $destDir  = dirname(__DIR__, 2).'/public/uploads/events';
                    if (!is_dir($destDir)) mkdir($destDir, 0777, true);
                    $destPath = $destDir . '/' . $fname;
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $destPath)) {
                        $imageUrl = '/adamson-ccit/public/uploads/events/'.$fname;
                    }
                }
            }

            if ($title !== '' && $desc !== '' && $startAt) {
                Event::create([
                    'title'       => $title,
                    'description' => $desc,
                    'location'    => $loc,
                    'category'    => $cat,
                    'status'      => $eStatus,
                    'start_at'    => $startAt,
                    'end_at'      => $endAt,
                    'image_url'   => $imageUrl
                ]);
                $status = $eStatus;
            }
            header('Location: ?page=admin_manage_events&status='.urlencode($status)); exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'], $_POST['id']) && class_exists('Event')) {
            $id      = (int)$_POST['id'];
            $eStatus = trim($_POST['update_status']);
            if ($id > 0) Event::updateStatus($id, $eStatus);
            $status = $eStatus;
            header('Location: ?page=admin_manage_events&status='.urlencode($status)); exit;
        }

        if (isset($_GET['delete']) && class_exists('Event')) {
            Event::delete((int)$_GET['delete']);
            header('Location: ?page=admin_manage_events&status='.urlencode($status)); exit;
        }

        $events = class_exists('Event') ? Event::list($status) : [];
        $counts = (class_exists('Event') && method_exists('Event','statusCounts')) ? Event::statusCounts() : ['all'=>0,'draft'=>0,'published'=>0,'archived'=>0];

        ob_start();
        extract(compact('events','status','counts'), EXTR_SKIP);
        include __DIR__ . '/../views/admin_manage_events.php';
        return ob_get_clean();
    }

    /** ==================== ANNOUNCEMENTS ==================== */
    public function manageAnnouncements(): string
    {
        $this->requireAdminOrDean();

        $core = __DIR__ . '/../core/Model.php'; if (is_file($core)) require_once $core;
        $mdl  = __DIR__ . '/../models/Announcement.php'; if (is_file($mdl)) include_once $mdl;

        $status = isset($_GET['status']) ? strtolower((string)$_GET['status']) : 'all';
        if (!in_array($status, ['all','draft','published','archived'], true)) $status = 'all';

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_announcement']) && class_exists('Announcement')) {
            $title    = trim($_POST['title']    ?? '');
            $content  = trim($_POST['content']  ?? '');
            $category = trim($_POST['category'] ?? 'general');
            $aStatus  = trim($_POST['status']   ?? 'draft');
            $date     = $_POST['date'] ?? date('Y-m-d');

            $imageUrl = null;
            if (!empty($_FILES['image']['name']) && ($_FILES['image']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
                $okTypes = ['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp','image/gif'=>'gif'];
                $type    = mime_content_type($_FILES['image']['tmp_name']);
                if (isset($okTypes[$type])) {
                    $ext      = $okTypes[$type];
                    $safeBase = preg_replace('~[^a-zA-Z0-9_-]+~', '-', strtolower(pathinfo($_FILES['image']['name'], PATHINFO_FILENAME)));
                    $fname    = date('Ymd_His').'_'.($safeBase ?: 'announcement').'.'.$ext;

                    $destDir  = dirname(__DIR__, 2).'/public/uploads/announcements';
                    if (!is_dir($destDir)) mkdir($destDir, 0777, true);
                    $destPath = $destDir . '/' . $fname;
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $destPath)) {
                        $imageUrl = '/adamson-ccit/public/uploads/announcements/'.$fname;
                    }
                }
            }

            if ($title !== '' && $content !== '') {
                Announcement::create($title, $content, $aStatus, $category, $date, $imageUrl);
                $status = $aStatus;
            }
            header('Location: ?page=admin_manage_announcements&status='.urlencode($status)); exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'], $_POST['id']) && class_exists('Announcement')) {
            $id = (int)$_POST['id']; $aStatus = trim($_POST['update_status']);
            if ($id>0) Announcement::updateStatus($id, $aStatus);
            $status = $aStatus;
            header('Location: ?page=admin_manage_announcements&status='.urlencode($status)); exit;
        }

        if (isset($_GET['delete']) && class_exists('Announcement')) {
            Announcement::delete((int)$_GET['delete']);
            header('Location: ?page=admin_manage_announcements&status='.urlencode($status)); exit;
        }

        $announcements = class_exists('Announcement') ? Announcement::list($status) : [];
        $counts = (class_exists('Announcement') && method_exists('Announcement','statusCounts'))
            ? Announcement::statusCounts()
            : ['all'=>0,'draft'=>0,'published'=>0,'archived'=>0];

        ob_start();
        extract(compact('announcements','status','counts'), EXTR_SKIP);
        include __DIR__ . '/../views/admin_manage_announcements.php';
        return ob_get_clean();
    }

    /** ==================== PROGRAMS (ALL) ==================== */
    public function managePrograms(): string
    {
        $this->requireAdminOrDean();

        $core = __DIR__ . '/../core/Model.php'; if (is_file($core)) require_once $core;
        $mProgram = __DIR__ . '/../models/Program.php'; if (is_file($mProgram)) include_once $mProgram;
        $mUgs     = __DIR__ . '/../models/ProgramsUndergraduateSettings.php'; if (is_file($mUgs)) include_once $mUgs;
        $mGs      = __DIR__ . '/../models/ProgramsGraduateSettings.php'; if (is_file($mGs)) include_once $mGs;

        $notice = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                if (!empty($_POST['delete_program']) && class_exists('Program')) {
                    Program::delete((int)$_POST['delete_program']);
                    $notice .= 'Program deleted. ';
                }

                if (!empty($_POST['program']) && is_array($_POST['program']) && class_exists('Program')) {
                    foreach ($_POST['program'] as $id => $row) {
                        if (!is_numeric($id)) continue;
                        Program::update((int)$id, [
                            'name'        => $row['name'] ?? '',
                            'description' => $row['description'] ?? '',
                            'image'       => $row['image'] ?? '',
                            'image_alt'   => $row['image_alt'] ?? '',
                            'url'         => $row['url'] ?? '',
                            'is_active'   => isset($row['is_active']) ? 1 : 0,
                        ]);
                    }
                    $notice .= 'Programs saved. ';
                }

                if (!empty($_POST['new_program']['name']) && class_exists('Program')) {
                    $np = $_POST['new_program'];
                    if (method_exists('Program','createFull')) {
                        Program::createFull($np);
                    } else {
                        Program::create(trim($np['name']), trim($np['description'] ?? ''));
                    }
                    $notice .= 'New program added. ';
                }

                if (isset($_POST['ugs']) && is_array($_POST['ugs']) && class_exists('ProgramsUndergraduateSettings')) {
                    ProgramsUndergraduateSettings::updateSettings([
                        'subhero_image_url' => $_POST['ugs']['subhero_image_url'] ?? null,
                        'subhero_lead'      => $_POST['ugs']['subhero_lead'] ?? null,
                        'programs_grid'     => $_POST['ugs']['programs_grid'] ?? null,
                        'cta_title'         => $_POST['ugs']['cta_title'] ?? null,
                        'cta_description'   => $_POST['ugs']['cta_description'] ?? null,
                        'cta_action_url'    => $_POST['ugs']['cta_action_url'] ?? null,
                        'cta_action_label'  => $_POST['ugs']['cta_action_label'] ?? null,
                    ]);
                    $notice .= 'Undergraduate settings saved. ';
                }

                if (isset($_POST['gs']) && is_array($_POST['gs']) && class_exists('ProgramsGraduateSettings')) {
                    ProgramsGraduateSettings::updateSettings([
                        'subhero_image_url' => $_POST['gs']['subhero_image_url'] ?? null,
                        'subhero_lead'      => $_POST['gs']['subhero_lead'] ?? null,
                        'programs_grid'     => $_POST['gs']['programs_grid'] ?? null,
                        'cta_title'         => $_POST['gs']['cta_title'] ?? null,
                        'cta_description'   => $_POST['gs']['cta_description'] ?? null,
                        'cta_action_url'    => $_POST['gs']['cta_action_url'] ?? null,
                        'cta_action_label'  => $_POST['gs']['cta_action_label'] ?? null,
                    ]);
                    $notice .= 'Graduate settings saved. ';
                }

            } catch (\Throwable $e) {
                $notice = 'Error: ' . $e->getMessage();
            }
        }

        $programs = class_exists('Program') ? Program::all() : [];
        $ugs      = class_exists('ProgramsUndergraduateSettings') ? ProgramsUndergraduateSettings::getSettings() : [];
        $gs       = class_exists('ProgramsGraduateSettings') ? ProgramsGraduateSettings::getSettings() : [];

        $username = $_SESSION['user']['username'] ?? 'Admin';

        ob_start();
        extract(compact('username','programs','ugs','gs','notice'), EXTR_SKIP);
        include __DIR__ . '/../views/admin_manage_programs.php';
        return ob_get_clean();
    }

    /** ==================== FACULTY ==================== */
    public function manageFaculty(): string
    {
        $this->requireAdminOrDean();
        ob_start();
        include __DIR__ . '/../views/admin_manage_faculty.php';
        return ob_get_clean();
    }

    /** ==================== PROGRAM CARDS (UG) ==================== */
    public function programsUndergraduate(): string
    {
        $this->requireAdminOrDean();
        
        // Include debug helper if available and not already included
        $debugHelper = __DIR__ . '/../lib/debug_helper.php';
        if (file_exists($debugHelper) && !function_exists('is_debug_enabled')) {
            include_once $debugHelper;
        }
        
        require_once __DIR__ . '/../models/ProgramsUndergraduateSettings.php';
        
        // First handle the programs cards view if that's what we want to show
        if (isset($_GET['cards'])) {
            require_once __DIR__ . '/../models/ProgramCard.php';

            $level  = 'undergraduate';
            $status = $_GET['status'] ?? 'all';
            if (!in_array($status, ['all','draft','published','archived'], true)) $status = 'all';
            $notice = '';

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                try {
                    if (!empty($_POST['add_card'])) {
                        $d = $_POST['card'] ?? [];
                        $d['level'] = $level;
                        ProgramCard::create($d);
                        header('Location: ?page=admin_programs_undergraduate&cards=1&status='.urlencode($d['status'] ?? 'draft').'&ok=1&act=add'); exit;
                    }
                    if (!empty($_POST['update_status']) && !empty($_POST['id'])) {
                        ProgramCard::updateStatus((int)$_POST['id'], $_POST['update_status']);
                        header('Location: ?page=admin_programs_undergraduate&cards=1&status='.urlencode($_POST['update_status']).'&ok=1&act=status'); exit;
                    }
                    if (!empty($_POST['edit_card']) && !empty($_POST['id'])) {
                        ProgramCard::update((int)$_POST['id'], $_POST['card'] ?? []);
                        header('Location: ?page=admin_programs_undergraduate&cards=1&status='.urlencode($status).'&ok=1&act=edit'); exit;
                    }
                } catch (\Throwable $e) {
                    $notice = 'Error: '.$e->getMessage();
                }
            }

            if (!empty($_GET['delete'])) {
                ProgramCard::delete((int)$_GET['delete']);
                header('Location: ?page=admin_programs_undergraduate&cards=1&status='.urlencode($status).'&ok=1&act=del'); exit;
            }

            $cards   = ProgramCard::list($level, $status);
            $counts  = ProgramCard::statusCounts($level);
            $username = $_SESSION['user']['username'] ?? 'Admin';

            if (!$notice && isset($_GET['ok'], $_GET['act'])) {
                $ok = $_GET['ok'] === '1';
                $notice = $ok ? match($_GET['act']) {
                    'add'=>'Card added.', 'status'=>'Status updated.', 'edit'=>'Card saved.', 'del'=>'Card deleted.', default=>'',
                } : 'Operation failed.';
            }

            ob_start();
            extract(compact('cards','counts','status','username','level','notice'), EXTR_SKIP);
            include __DIR__ . '/../views/admin/admin_programs_cards.php';
            return ob_get_clean();
        }
        
        // Otherwise show the main undergraduate programs admin view
        $user = $_SESSION['user'] ?? [];
        $username = $user['username'] ?? 'Admin';
        $notice = '';
        $ugs = ProgramsUndergraduateSettings::getSettings();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $action = $_POST['action'] ?? 'save_settings';

                if ($action === 'update_card') {
                    $id = (int)($_POST['card_id'] ?? 0);
                    $data = $_POST['card'] ?? [];
                    
                    // Show more debug info if available
                    if (function_exists('debug_log')) {
                        debug_log("AdminController: Updating card ID $id", ['data' => $data]);
                    }
                    
                    try {
                        $result = ProgramsUndergraduateSettings::updateCardById($id, $data);
                        
                        // Get result message
                        if (is_array($result) && isset($result['message'])) {
                            $notice = $result['message'];
                        } else {
                            $notice = 'Card updated successfully.';
                        }
                        
                        // Add debug info if enabled
                        if (function_exists('is_debug_enabled') && is_debug_enabled()) {
                            if (is_array($result)) {
                                $debugInfo = json_encode($result, JSON_PRETTY_PRINT);
                                $notice .= ' <small><a href="#" onclick="alert(\'Debug info: ' . addslashes($debugInfo) . '\'); return false;">[Debug]</a></small>';
                            }
                        }
                        
                    } catch (\Throwable $e) {
                        $notice = 'Error updating card: ' . $e->getMessage();
                        
                        // Log the error
                        error_log('Card update error in AdminController: ' . $e->getMessage());
                        
                        if (function_exists('debug_log')) {
                            debug_log("Card update error", [
                                'error' => $e->getMessage(),
                                'trace' => $e->getTraceAsString()
                            ]);
                        }
                    }
                    
                } elseif ($action === 'delete_card') {
                    $id = (int)($_POST['card_id'] ?? 0);
                    try {
                        ProgramsUndergraduateSettings::deleteCardById($id);
                        $notice = 'Card deleted successfully.';
                    } catch (\Throwable $e) {
                        $notice = 'Error deleting card: ' . $e->getMessage();
                        error_log('Card delete error: ' . $e->getMessage());
                    }
                } else {
                    // default: save settings + maybe add a new card
                    try {
                        ProgramsUndergraduateSettings::updateSettings($_POST['ugs'] ?? [], $_POST['add_card'] ?? null);
                        $notice = 'Undergraduate settings saved successfully.';
                    } catch (\Throwable $e) {
                        $notice = 'Error saving settings: ' . $e->getMessage();
                        error_log('Settings update error: ' . $e->getMessage());
                    }
                }

                // refresh snapshot after any action
                $ugs = ProgramsUndergraduateSettings::getSettings();
            } catch (\Throwable $e) {
                $notice = 'Error: ' . $e->getMessage();
            }
        }

        // Fetch dynamic cards (raw rows for editing)
        $cards = ProgramsUndergraduateSettings::getAllCards();
        
        ob_start();
        extract(compact('user', 'username', 'notice', 'ugs', 'cards'), EXTR_SKIP);
        include __DIR__ . '/../views/admin/admin_programs_undergraduate.php';
        return ob_get_clean();
    }

    /** ==================== PROGRAM CARDS (GRAD) ==================== */
    public function programsGraduate(): string
    {
        $this->requireAdminOrDean();
        require_once __DIR__ . '/../models/ProgramCard.php';

        $level  = 'graduate';
        $status = $_GET['status'] ?? 'all';
        if (!in_array($status, ['all','draft','published','archived'], true)) $status = 'all';
        $notice = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                if (!empty($_POST['add_card'])) {
                    $d = $_POST['card'] ?? [];
                    $d['level'] = $level;
                    ProgramCard::create($d);
                    header('Location: ?page=admin_programs_graduate&status='.urlencode($d['status'] ?? 'draft').'&ok=1&act=add'); exit;
                }
                if (!empty($_POST['update_status']) && !empty($_POST['id'])) {
                    ProgramCard::updateStatus((int)$_POST['id'], $_POST['update_status']);
                    header('Location: ?page=admin_programs_graduate&status='.urlencode($_POST['update_status']).'&ok=1&act=status'); exit;
                }
                if (!empty($_POST['edit_card']) && !empty($_POST['id'])) {
                    ProgramCard::update((int)$_POST['id'], $_POST['card'] ?? []);
                    header('Location: ?page=admin_programs_graduate&status='.urlencode($status).'&ok=1&act=edit'); exit;
                }
            } catch (\Throwable $e) {
                $notice = 'Error: '.$e->getMessage();
            }
        }

        if (!empty($_GET['delete'])) {
            ProgramCard::delete((int)$_GET['delete']);
            header('Location: ?page=admin_programs_graduate&status='.urlencode($status).'&ok=1&act=del'); exit;
        }

        $cards   = ProgramCard::list($level, $status);
        $counts  = ProgramCard::statusCounts($level);
        $username = $_SESSION['user']['username'] ?? 'Admin';

        if (!$notice && isset($_GET['ok'], $_GET['act'])) {
            $ok = $_GET['ok'] === '1';
            $notice = $ok ? match($_GET['act']) {
                'add'=>'Card added.', 'status'=>'Status updated.', 'edit'=>'Card saved.', 'del'=>'Card deleted.', default=>'',
            } : 'Operation failed.';
        }

        ob_start();
        extract(compact('cards','counts','status','username','level','notice'), EXTR_SKIP);
        include __DIR__ . '/../views/admin/admin_programs_cards.php';
        return ob_get_clean();
    }

    /** ==================== STUDENT ADMIN METHODS ==================== */

    public function studentOrganizations(): string
    {
        $this->requireAdminOrDean();
        ob_start();
        include __DIR__ . '/../views/admin/admin_student_organizations.php';
        return ob_get_clean();
    }

    public function studentScholarships(): string
    {
        $this->requireAdminOrDean();
        ob_start();
        include __DIR__ . '/../views/admin/admin_student_scholarships.php';
        return ob_get_clean();
    }

    public function studentResearch(): string
    {
        $this->requireAdminOrDean();
        ob_start();
        include __DIR__ . '/../views/admin/admin_student_research.php';
        return ob_get_clean();
    }

    public function studentCertifications(): string
    {
        $this->requireAdminOrDean();
        ob_start();
        include __DIR__ . '/../views/admin/admin_student_certifications.php';
        return ob_get_clean();
    }

    public function studentTestimonials(): string
    {
        $this->requireAdminOrDean();
        ob_start();
        include __DIR__ . '/../views/admin/admin_student_testimonials.php';
        return ob_get_clean();
    }
}