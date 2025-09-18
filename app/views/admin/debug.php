<?php
// Debug utility for admin
// Access this via /adamson-ccit/admin/debug

// Only allow access if debug mode is enabled
$debug_enabled = !empty($_GET['debug']) || !empty($_COOKIE['debug']);
if (!$debug_enabled) {
    header('Location: /adamson-ccit/admin');
    exit;
}

// Set debug cookie if requested
if (!empty($_GET['enable_debug'])) {
    setcookie('debug', '1', time() + 86400, '/');
    header('Location: /adamson-ccit/admin/debug');
    exit;
}

// Clear debug cookie if requested
if (!empty($_GET['disable_debug'])) {
    setcookie('debug', '', time() - 3600, '/');
    header('Location: /adamson-ccit/admin');
    exit;
}

// Include common admin header
include __DIR__ . '/admin_header.php';
?>

<div class="admin-content">
    <div class="admin-header">
        <h1>Debug Tools</h1>
        <p>This page provides debugging tools for troubleshooting issues with the CMS.</p>
    </div>
    
    <div class="card">
        <h2>Database Tables</h2>
        <?php
        try {
            $db = new PDO('mysql:host=localhost;dbname=adamson', 'root', '');
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Get list of tables
            $tables = $db->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
            
            echo '<h3>Available Tables</h3>';
            echo '<ul>';
            foreach ($tables as $table) {
                echo "<li>$table <a href=\"?show_table=$table\" class=\"btn btn-sm\">View</a></li>";
            }
            echo '</ul>';
            
            // Show table structure and data if requested
            if (!empty($_GET['show_table'])) {
                $table = $_GET['show_table'];
                echo '<h3>Table: ' . htmlspecialchars($table) . '</h3>';
                
                // Table structure
                echo '<h4>Structure</h4>';
                echo '<pre>';
                $structure = $db->query("DESCRIBE `$table`")->fetchAll(PDO::FETCH_ASSOC);
                print_r($structure);
                echo '</pre>';
                
                // Table data
                echo '<h4>Data</h4>';
                echo '<pre>';
                $data = $db->query("SELECT * FROM `$table` LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);
                print_r($data);
                echo '</pre>';
            }
            
        } catch (PDOException $e) {
            echo '<div class="alert alert-danger">Database Error: ' . $e->getMessage() . '</div>';
        }
        ?>
    </div>
    
    <div class="card">
        <h2>PHP Configuration</h2>
        <pre><?php echo htmlspecialchars(print_r(ini_get_all(), true)); ?></pre>
    </div>
    
    <div class="card">
        <h2>Test Card Update</h2>
        <p>This form tests the card update functionality directly.</p>
        
        <form method="post" action="/adamson-ccit/admin/programs/undergraduate?action=update_card">
            <div class="form-group">
                <label>Card ID:</label>
                <input type="number" name="card_id" value="1" required>
            </div>
            
            <div class="form-group">
                <label>Title:</label>
                <input type="text" name="card[title]" value="Test Card <?php echo date('Y-m-d H:i:s'); ?>" required>
            </div>
            
            <div class="form-group">
                <label>Slug:</label>
                <input type="text" name="card[slug]" value="test-card-<?php echo time(); ?>">
            </div>
            
            <div class="form-group">
                <label>Badge:</label>
                <input type="text" name="card[badge]" value="TEST">
            </div>
            
            <input type="hidden" name="debug_timestamp" value="<?php echo date('c'); ?>">
            <input type="hidden" name="force_update" value="1">
            <button type="submit" class="btn">Test Update</button>
        </form>
    </div>
    
    <div class="card">
        <h2>Debug Status</h2>
        <p>Debug mode is currently: <strong><?php echo $debug_enabled ? 'ENABLED' : 'DISABLED'; ?></strong></p>
        
        <?php if ($debug_enabled): ?>
            <p><a href="?disable_debug=1" class="btn">Disable Debug Mode</a></p>
        <?php else: ?>
            <p><a href="?enable_debug=1" class="btn">Enable Debug Mode</a></p>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/admin_footer.php'; ?>