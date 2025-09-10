<?php
// app/controllers/StudentCertController.php
require_once __DIR__ . '/../lib/Auth.php';
require_once __DIR__ . '/../models/Model.php';

final class StudentCertController {
    private const TABLE = 'student_licenses'; // ← your new table name

    private function db(): PDO {
        return (new class extends Model { public function d(){ return parent::db(); } })->d();
    }

    // In StudentCertController::currentStudentId()
private function currentStudentId(): int {
    $u = Auth::user();
    if (!$u) { http_response_code(403); exit('Unauthorized'); }

    // Prefer numeric id already in session (if your Auth::user() has it)
    foreach (['id','user_id','account_id'] as $k) {
        if (isset($u[$k]) && ctype_digit((string)$u[$k])) return (int)$u[$k];
    }

    // Resolve strictly via users.username (no email lookups)
    if (!empty($u['username'])) {
        $db = $this->db();
        $q = $db->prepare("SELECT id FROM users WHERE username = :v LIMIT 1");
        $q->execute([':v' => $u['username']]);
        if ($row = $q->fetch(PDO::FETCH_ASSOC)) return (int)$row['id'];
    }

    http_response_code(403);
    echo "<div style='font:14px/1.4 system-ui; padding:16px; border:1px solid #fecaca; background:#fef2f2; color:#7f1d1d; margin:20px'>
            <b>Cannot resolve student ID for this session.</b><br>
            Session keys I see: <code>".htmlspecialchars(implode(', ', array_keys((array)$u)))."</code><br><br>
            Make sure you either:
            <ol>
              <li>Store a numeric id in session (keys: <code>id</code> or <code>user_id</code>), or</li>
              <li>Create a row in <code>users</code> with <code>username = ".htmlspecialchars($u['username'] ?? '(none)')."</code>.</li>
            </ol>
          </div>";
    exit;
}




    public function save(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit('Method not allowed'); }
        $db  = $this->db();
        $uid = $this->currentStudentId();

        $mode          = $_POST['mode'] ?? 'create';
        $id            = (int)($_POST['id'] ?? 0);
        $name          = trim($_POST['name'] ?? '');
        $company_id    = (int)($_POST['company_id'] ?? 0);
        $issue_month   = $_POST['issue_month'] !== '' ? (int)$_POST['issue_month'] : null;
        $issue_year    = $_POST['issue_year']  !== '' ? (int)$_POST['issue_year']  : null;
        $no_expire     = isset($_POST['no_expire']) ? 1 : 0;
        $expire_month  = !$no_expire && $_POST['expire_month'] !== '' ? (int)$_POST['expire_month'] : null;
        $expire_year   = !$no_expire && $_POST['expire_year']  !== '' ? (int)$_POST['expire_year']  : null;
        $credential_id = trim($_POST['credential_id'] ?? '');
        $credential_url= trim($_POST['credential_url'] ?? '');
        $visibility    = in_array($_POST['visibility'] ?? 'public', ['public','private'], true) ? $_POST['visibility'] : 'public';

        if ($name === '' || !$company_id) { http_response_code(422); exit('Missing required fields'); }

        // Only allow active companies
$ok = $db->prepare("SELECT 1 FROM companies WHERE id=?");
        $ok->execute([$company_id]);
        if (!$ok->fetch()) { http_response_code(422); exit('Invalid company'); }

        if ($mode === 'create') {
            $ins = $db->prepare("
                INSERT INTO ".self::TABLE."
                (user_id, company_id, name, issue_month, issue_year, expires, expire_month, expire_year, credential_id, credential_url, visibility)
                VALUES (?,?,?,?,?,?,?,?,?,?,?)
            ");
            $ins->execute([$uid,$company_id,$name,$issue_month,$issue_year,$no_expire?0:1,$expire_month,$expire_year,$credential_id?:null,$credential_url?:null,$visibility]);
        } else {
            // Ownership check
            $own = $db->prepare("SELECT id FROM ".self::TABLE." WHERE id=? AND user_id=?");
            $own->execute([$id,$uid]);
            if (!$own->fetch()) { http_response_code(403); exit('Not your item'); }

            $upd = $db->prepare("
                UPDATE ".self::TABLE."
                SET company_id=?, name=?, issue_month=?, issue_year=?, expires=?, expire_month=?, expire_year=?, credential_id=?, credential_url=?, visibility=?, updated_at=NOW()
                WHERE id=?
            ");
            $upd->execute([$company_id,$name,$issue_month,$issue_year,$no_expire?0:1,$expire_month,$expire_year,$credential_id?:null,$credential_url?:null,$visibility,$id]);
        }

        header("Location: /adamson-ccit/public/index.php?page=student_profile&saved=1");
    }

    public function delete(): void {
        $db  = $this->db();
        $uid = $this->currentStudentId();
        $id  = (int)($_GET['id'] ?? 0);
        if (!$id) { http_response_code(400); exit('Bad request'); }

        $own = $db->prepare("SELECT id FROM ".self::TABLE." WHERE id=? AND user_id=?");
        $own->execute([$id,$uid]);
        if (!$own->fetch()) { http_response_code(403); exit('Not your item'); }

        $db->prepare("DELETE FROM ".self::TABLE." WHERE id=?")->execute([$id]);
        header("Location: /adamson-ccit/public/index.php?page=student_profile&deleted=1");
    }
}
