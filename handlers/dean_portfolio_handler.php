<?php
require_once __DIR__ . '/../app/lib/Auth.php';
require_once __DIR__ . '/../app/models/Model.php';

// Ensure user is authenticated as dean
Auth::requireRole(['dean'], '/adamson-ccit/public/index.php?page=login');

class _DBX extends Model { public function d(){ return parent::db(); } }
$_db = (new _DBX())->d();

$user = Auth::user() ?? [];
$deanId = $user['id'] ?? null;

if (!$deanId) {
    header('Location: /adamson-ccit/public/index.php?page=login');
    exit;
}

// Handle portfolio save - check for POST data
if ($_POST && isset($_POST['mode'])) {
    $mode = $_POST['mode']; // 'create' or 'update'
    $id = (int)($_POST['id'] ?? 0);
    
    // Validate required fields
    $errors = [];
    $name = trim($_POST['name'] ?? '');
    $companyId = (int)($_POST['company_id'] ?? 0);
    
    if (empty($name)) {
        $errors[] = 'Certification name is required';
    }
    if (empty($companyId)) {
        $errors[] = 'Issuing organization is required';
    }

    if (empty($errors)) {
        try {
            // Prepare data
            $data = [
                'user_id' => $deanId,
                'name' => $name,
                'company_id' => $companyId,
                'issue_month' => !empty($_POST['issue_month']) ? (int)$_POST['issue_month'] : null,
                'issue_year' => !empty($_POST['issue_year']) ? (int)$_POST['issue_year'] : null,
                'expires' => isset($_POST['no_expire']) ? 0 : 1, // 0 = no expiry, 1 = has expiry
                'expire_month' => !empty($_POST['expire_month']) ? (int)$_POST['expire_month'] : null,
                'expire_year' => !empty($_POST['expire_year']) ? (int)$_POST['expire_year'] : null,
                'credential_id' => trim($_POST['credential_id'] ?? ''),
                'credential_url' => trim($_POST['credential_url'] ?? ''),
                'visibility' => trim($_POST['visibility'] ?? 'public')
            ];

            // Clear expiry data if no_expire is checked
            if (!$data['expires']) {
                $data['expire_month'] = null;
                $data['expire_year'] = null;
            }

            $_db->beginTransaction();
            
            if ($mode === 'create') {
                // Insert new certification
                $insertSQL = "INSERT INTO faculty_portfolio 
                    (user_id, name, company_id, issue_month, issue_year, expires, expire_month, expire_year, credential_id, credential_url, visibility, created_at, updated_at) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)";
                $stmt = $_db->prepare($insertSQL);
                $stmt->execute([
                    $data['user_id'], $data['name'], $data['company_id'], 
                    $data['issue_month'], $data['issue_year'], $data['expires'], 
                    $data['expire_month'], $data['expire_year'], $data['credential_id'], 
                    $data['credential_url'], $data['visibility']
                ]);
            } else {
                // Update existing certification - ensure it belongs to this dean
                $checkStmt = $_db->prepare("SELECT id FROM faculty_portfolio WHERE id = ? AND user_id = ?");
                $checkStmt->execute([$id, $deanId]);
                
                if (!$checkStmt->fetch()) {
                    throw new Exception('Certification not found or access denied');
                }
                
                $updateSQL = "UPDATE faculty_portfolio SET 
                    name = ?, company_id = ?, issue_month = ?, issue_year = ?, expires = ?, 
                    expire_month = ?, expire_year = ?, credential_id = ?, credential_url = ?, 
                    visibility = ?, updated_at = CURRENT_TIMESTAMP 
                    WHERE id = ? AND user_id = ?";
                $stmt = $_db->prepare($updateSQL);
                $stmt->execute([
                    $data['name'], $data['company_id'], $data['issue_month'], $data['issue_year'], 
                    $data['expires'], $data['expire_month'], $data['expire_year'], $data['credential_id'], 
                    $data['credential_url'], $data['visibility'], $id, $deanId
                ]);
            }
            
            $_db->commit();
            
            // Redirect back to portfolio with success message
            header('Location: /adamson-ccit/public/index.php?page=dean_portfolio&saved=1');
            exit;
            
        } catch (Exception $e) {
            if (isset($_db)) {
                $_db->rollback();
            }
            error_log("Dean portfolio save error: " . $e->getMessage());
            $error = 'Failed to save certification. Please try again.';
        }
    } else {
        $error = implode(', ', $errors);
    }
    
    // If we get here, there was an error
    header('Location: /adamson-ccit/public/index.php?page=dean_portfolio&error=' . urlencode($error));
    exit;
}

// Handle delete request
if ($_GET && isset($_GET['id']) && isset($_GET['page']) && $_GET['page'] === 'dean_portfolio_delete') {
    $id = (int)$_GET['id'];
    
    try {
        $_db->beginTransaction();
        
        // Delete certification - ensure it belongs to this dean
        $deleteStmt = $_db->prepare("DELETE FROM faculty_portfolio WHERE id = ? AND user_id = ?");
        $result = $deleteStmt->execute([$id, $deanId]);
        
        if ($deleteStmt->rowCount() === 0) {
            throw new Exception('Certification not found or access denied');
        }
        
        $_db->commit();
        
        // Redirect back with success message
        header('Location: /adamson-ccit/public/index.php?page=dean_portfolio&deleted=1');
        exit;
        
    } catch (Exception $e) {
        if (isset($_db)) {
            $_db->rollback();
        }
        error_log("Dean portfolio delete error: " . $e->getMessage());
        $error = 'Failed to delete certification. Please try again.';
        header('Location: /adamson-ccit/public/index.php?page=dean_portfolio&error=' . urlencode($error));
        exit;
    }
}
?>