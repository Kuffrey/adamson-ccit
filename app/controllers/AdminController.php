<?php declare(strict_types=1);

class AdminController
{
    /** Allow Admin or Dean */
    protected function requireAdminOrDean(): void
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $role = $_SESSION['user']['role'] ?? '';
        if (!in_array($role, ['admin','dean'], true)) {
            header('Location: ?page=login_admin'); // correct route name
            exit;
        }
    }

    /** Homepage CMS CRUD */
    public function manageHomepage(): string
    {
        $this->requireAdminOrDean();
        require_once __DIR__ . '/../models/HomepageSettings.php';

        $model   = new HomepageSettings();
        $success = false;
        $error   = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $_POST;
            if ($model->update($data)) $success = true;
            else $error = 'Failed to save homepage settings.';
        }

        $homepage = $model->get();
        $username = $_SESSION['user']['username'] ?? 'Admin';

        ob_start();
        include __DIR__ . '/../views/admin_manage_homepage.php';
        return ob_get_clean();
    }

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
            $status = $eStatus; // <-- keep tab in sync
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
            $date     = $_POST['date'] ?? date('Y-m-d'); // <-- include date

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
                // Signature includes $date
                Announcement::create($title, $content, $aStatus, $category, $date, $imageUrl);
                $status = $aStatus;
            }
            header('Location: ?page=admin_manage_announcements&status='.urlencode($status)); exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'], $_POST['id']) && class_exists('Announcement')) {
            $id = (int)$_POST['id']; $aStatus = trim($_POST['update_status']);
            if ($id>0) Announcement::updateStatus($id, $aStatus);
            $status = $aStatus; // <-- keep tab in sync
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

    public function managePrograms(): string {
        $this->requireAdminOrDean();
        ob_start();
        include __DIR__ . '/../views/admin_manage_programs.php';
        return ob_get_clean();
    }

    public function manageFaculty(): string {
        $this->requireAdminOrDean();
        ob_start();
        include __DIR__ . '/../views/admin_manage_faculty.php';
        return ob_get_clean();
    }
}
