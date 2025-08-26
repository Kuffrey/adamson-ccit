<?php
declare(strict_types=1);

class AdminController
{
    /** Simple auth guard (admin only) */
    private function requireAdmin(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (empty($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'admin') {
            header('Location: ?page=admin_login');
            exit;
        }
    }

    public function dashboard(): string
    {
        $this->requireAdmin();

        // Load base Model so models can connect
        $core = __DIR__ . '/../core/Model.php';
        if (is_file($core)) require_once $core;

        $recentNews = $recentPrograms = $pendingSubmissions = [];

        // News
        $newsPath = __DIR__ . '/../models/News.php';
        if (is_file($newsPath)) {
            include_once $newsPath;
            if (class_exists('News') && method_exists('News','latest')) {
                $recentNews = News::latest(6);
            }
        }

        // Optional other models (Program / Submission) — safe if absent
        $progPath = __DIR__ . '/../models/Program.php';
        if (is_file($progPath)) {
            include_once $progPath;
            if (class_exists('Program') && method_exists('Program','latest')) {
                $recentPrograms = Program::latest(4);
            }
        }

        $subPath = __DIR__ . '/../models/Submission.php';
        if (is_file($subPath)) {
            include_once $subPath;
            if (class_exists('Submission') && method_exists('Submission','pendingForAdmin')) {
                $pendingSubmissions = Submission::pendingForAdmin(5);
            }
        }

        ob_start();
        extract(compact('recentNews','recentPrograms','pendingSubmissions'), EXTR_SKIP);
        include __DIR__ . '/../views/admin_dashboard.php';
        return ob_get_clean();
    }

    public function manageNews(): string
    {
        $this->requireAdmin();

        $core = __DIR__ . '/../core/Model.php'; if (is_file($core)) require_once $core;
        $newsPath = __DIR__ . '/../models/News.php'; if (is_file($newsPath)) include_once $newsPath;

        // Which tab?
        $status = isset($_GET['status']) ? strtolower((string)$_GET['status']) : 'all';
        if (!in_array($status, ['all','draft','published','archived'], true)) $status = 'all';

        // Create
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_news']) && class_exists('News')) {
            $title    = trim($_POST['title']    ?? '');
            $content  = trim($_POST['content']  ?? '');
            $category = trim($_POST['category'] ?? 'news');
            $nStatus  = trim($_POST['status']   ?? 'draft');

            // image upload (optional)
            $imageUrl = null;
            if (!empty($_FILES['image']['name']) && ($_FILES['image']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
                $okTypes = ['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp','image/gif'=>'gif'];
                $type    = mime_content_type($_FILES['image']['tmp_name']);
                if (isset($okTypes[$type])) {
                    $ext      = $okTypes[$type];
                    $safeBase = preg_replace('~[^a-zA-Z0-9_-]+~', '-', strtolower(pathinfo($_FILES['image']['name'], PATHINFO_FILENAME)));
                    $fname    = date('Ymd_His').'_'.($safeBase ?: 'news').'.'.$ext;

                    $destDir  = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'news';
                    if (!is_dir($destDir)) mkdir($destDir, 0777, true);

                    $destPath = $destDir . DIRECTORY_SEPARATOR . $fname;
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $destPath)) {
                        $imageUrl = '/adamson-ccit/public/uploads/news/'.$fname;
                    }
                }
            }
            if ($title !== '' && $content !== '') {
                News::create($title, $content, $nStatus, $category, $imageUrl);
                $status = $nStatus; // return to the tab you used
            }
            header('Location: ?page=admin_manage_news&status='.urlencode($status));
            exit;
        }

        // Quick status change
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'], $_POST['id']) && class_exists('News')) {
            $id      = (int)$_POST['id'];
            $nStatus = trim($_POST['update_status']);
            if ($id > 0) News::updateStatus($id, $nStatus);
            header('Location: ?page=admin_manage_news&status='.urlencode($status)); exit;
        }

        // Delete
        if (isset($_GET['delete']) && class_exists('News')) {
            News::delete((int)$_GET['delete']);
            header('Location: ?page=admin_manage_news&status='.urlencode($status)); exit;
        }

        $news   = (class_exists('News')) ? News::list($status) : [];
        $counts = (class_exists('News') && method_exists('News','statusCounts')) ? News::statusCounts() : ['all'=>0,'draft'=>0,'published'=>0,'archived'=>0];

        ob_start();
        extract(compact('news','status','counts'), EXTR_SKIP);
        include __DIR__ . '/../views/admin_manage_news.php';
        return ob_get_clean();
    }

    public function manageEvents(): string
    {
        $this->requireAdmin();

        $core = __DIR__ . '/../core/Model.php'; if (is_file($core)) require_once $core;
        $mdl  = __DIR__ . '/../models/Event.php'; if (is_file($mdl)) include_once $mdl;

        $status = isset($_GET['status']) ? strtolower((string)$_GET['status']) : 'all';
        if (!in_array($status, ['all','draft','published','archived'], true)) $status = 'all';

        // helper: convert HTML datetime-local to MySQL DATETIME
        $toDT = static function (?string $x): ?string {
            $x = trim((string)$x);
            if ($x === '') return null;
            $x = str_replace('T',' ',$x);
            if (strlen($x) === 16) $x .= ':00';
            return $x;
        };

        // Create
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_event']) && class_exists('Event')) {
            $title   = trim($_POST['title'] ?? '');
            $desc    = trim($_POST['description'] ?? '');
            $loc     = trim($_POST['location'] ?? '');
            $cat     = trim($_POST['category'] ?? 'career');
            $eStatus = trim($_POST['status'] ?? 'draft');
            $startAt = $toDT($_POST['start_at'] ?? null);
            $endAt   = $toDT($_POST['end_at']   ?? null);

            // image upload
            $imageUrl = null;
            if (!empty($_FILES['image']['name']) && ($_FILES['image']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
                $okTypes = ['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp','image/gif'=>'gif'];
                $type    = mime_content_type($_FILES['image']['tmp_name']);
                if (isset($okTypes[$type])) {
                    $ext      = $okTypes[$type];
                    $safeBase = preg_replace('~[^a-zA-Z0-9_-]+~', '-', strtolower(pathinfo($_FILES['image']['name'], PATHINFO_FILENAME)));
                    $fname    = date('Ymd_His').'_'.($safeBase ?: 'event').'.'.$ext;

                    $destDir  = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'events';
                    if (!is_dir($destDir)) mkdir($destDir, 0777, true);

                    $destPath = $destDir . DIRECTORY_SEPARATOR . $fname;
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
                $status = $eStatus; // return to the tab used
            }
            header('Location: ?page=admin_manage_events&status='.urlencode($status)); exit;
        }

        // Status change
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'], $_POST['id']) && class_exists('Event')) {
            $id      = (int)$_POST['id'];
            $eStatus = trim($_POST['update_status']);
            if ($id > 0) Event::updateStatus($id, $eStatus);
            header('Location: ?page=admin_manage_events&status='.urlencode($status)); exit;
        }

        // Delete
        if (isset($_GET['delete']) && class_exists('Event')) {
            Event::delete((int)$_GET['delete']);
            header('Location: ?page=admin_manage_events&status='.urlencode($status)); exit;
        }

        $events = (class_exists('Event')) ? Event::list($status) : [];
        $counts = (class_exists('Event') && method_exists('Event','statusCounts')) ? Event::statusCounts() : ['all'=>0,'draft'=>0,'published'=>0,'archived'=>0];

        ob_start();
        extract(compact('events','status','counts'), EXTR_SKIP);
        include __DIR__ . '/../views/admin_manage_events.php';
        return ob_get_clean();
    }

    // (stubs)
    public function managePrograms(): string {
        $this->requireAdmin();
        ob_start();
        include __DIR__ . '/../views/admin_manage_programs.php';
        return ob_get_clean();
    }

    public function manageFaculty(): string {
        $this->requireAdmin();
        ob_start();
        include __DIR__ . '/../views/admin_manage_faculty.php';
        return ob_get_clean();
    }

    /** FULL announcements manager (inside class; no duplicate below!) */
    public function manageAnnouncements(): string
    {
        $this->requireAdmin();

        $core = __DIR__ . '/../core/Model.php'; if (is_file($core)) require_once $core;
        $mdl  = __DIR__ . '/../models/Announcement.php'; if (is_file($mdl)) include_once $mdl;

        $status = isset($_GET['status']) ? strtolower((string)$_GET['status']) : 'all';
        if (!in_array($status, ['all','draft','published','archived'], true)) $status = 'all';

        // Create
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_announcement']) && class_exists('Announcement')) {
            $title    = trim($_POST['title']    ?? '');
            $content  = trim($_POST['content']  ?? '');
            $category = trim($_POST['category'] ?? 'general');
            $aStatus  = trim($_POST['status']   ?? 'draft');

            // image upload (optional)
            $imageUrl = null;
            if (!empty($_FILES['image']['name']) && ($_FILES['image']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
                $okTypes = ['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp','image/gif'=>'gif'];
                $type    = mime_content_type($_FILES['image']['tmp_name']);
                if (isset($okTypes[$type])) {
                    $ext      = $okTypes[$type];
                    $safeBase = preg_replace('~[^a-zA-Z0-9_-]+~', '-', strtolower(pathinfo($_FILES['image']['name'], PATHINFO_FILENAME)));
                    $fname    = date('Ymd_His').'_'.($safeBase ?: 'announcement').'.'.$ext;

                    $destDir  = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'announcements';
                    if (!is_dir($destDir)) mkdir($destDir, 0777, true);

                    $destPath = $destDir . DIRECTORY_SEPARATOR . $fname;
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $destPath)) {
                        $imageUrl = '/adamson-ccit/public/uploads/announcements/'.$fname;
                    }
                }
            }
            if ($title !== '' && $content !== '') {
                Announcement::create($title, $content, $aStatus, $category, $imageUrl);
                $status = $aStatus;
            }
            header('Location: ?page=admin_manage_announcements&status='.urlencode($status)); exit;
        }

        // Status change
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'], $_POST['id']) && class_exists('Announcement')) {
            $id = (int)$_POST['id']; $aStatus = trim($_POST['update_status']);
            if ($id>0) Announcement::updateStatus($id, $aStatus);
            header('Location: ?page=admin_manage_announcements&status='.urlencode($status)); exit;
        }

        // Delete
        if (isset($_GET['delete']) && class_exists('Announcement')) {
            Announcement::delete((int)$_GET['delete']);
            header('Location: ?page=admin_manage_announcements&status='.urlencode($status)); exit;
        }

        $announcements = (class_exists('Announcement')) ? Announcement::list($status) : [];
        $counts = (class_exists('Announcement') && method_exists('Announcement','statusCounts'))
            ? Announcement::statusCounts()
            : ['all'=>0,'draft'=>0,'published'=>0,'archived'=>0];

        ob_start();
        extract(compact('announcements','status','counts'), EXTR_SKIP);
        include __DIR__ . '/../views/admin_manage_announcements.php';
        return ob_get_clean();
    }
}
