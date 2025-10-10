<!DOCTYPE html>
<html>
<head>
    <title>Test Admin Announcement Form</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input, textarea, select { width: 100%; padding: 8px; margin-bottom: 5px; box-sizing: border-box; }
        button { padding: 10px 20px; background: #007bff; color: white; border: none; cursor: pointer; }
        .result { margin-top: 20px; padding: 15px; background: #f8f9fa; border: 1px solid #dee2e6; }
        .success { background-color: #d4edda; border-color: #c3e6cb; color: #155724; }
        .error { background-color: #f8d7da; border-color: #f5c6cb; color: #721c24; }
    </style>
</head>
<body>
    <h1>Test Admin Announcement Form</h1>
    
    <?php
    require_once 'app/models/Announcement.php';
    
    // Get existing announcement for testing
    $announcement = Announcement::get(1);
    $notice = '';
    
    if ($_POST) {
        echo "<div class='result'>";
        echo "<h3>Form Submission Debug Info:</h3>";
        echo "<strong>POST Data:</strong><pre>" . htmlspecialchars(print_r($_POST, true)) . "</pre>";
        echo "<strong>FILES Data:</strong><pre>" . htmlspecialchars(print_r($_FILES, true)) . "</pre>";
        
        if (!empty($_POST['edit_announcement']) && !empty($_POST['id'])) {
            $announcementId = (int)$_POST['id'];
            $imageUrl = null;
            $currentImageUrl = trim($_POST['current_image_url'] ?? '');
            
            echo "<strong>Processing Update for ID:</strong> $announcementId<br>";
            echo "<strong>Current Image URL:</strong> " . htmlspecialchars($currentImageUrl) . "<br>";
            
            // Handle image upload or removal
            if (!empty($_POST['remove_image'])) {
                $imageUrl = null;
                echo "<strong>Action:</strong> Removing image<br>";
            } elseif (!empty($_FILES['image']['tmp_name'])) {
                echo "<strong>Action:</strong> New image uploaded<br>";
                $imgTmp = $_FILES['image']['tmp_name'];
                $imgName = time() . '-' . basename($_FILES['image']['name']);
                $destDir = __DIR__ . '/public/uploads/announcements/';
                if (!is_dir($destDir)) { @mkdir($destDir, 0777, true); }
                $dest = $destDir . $imgName;
                if (move_uploaded_file($imgTmp, $dest)) {
                    $imageUrl = '/adamson-ccit/public/uploads/announcements/' . $imgName;
                    echo "<strong>Upload Result:</strong> SUCCESS - " . htmlspecialchars($imageUrl) . "<br>";
                } else {
                    $imageUrl = $currentImageUrl;
                    echo "<strong>Upload Result:</strong> FAILED - keeping current<br>";
                }
            } else {
                $imageUrl = $currentImageUrl ?: null;
                echo "<strong>Action:</strong> Keeping current image<br>";
            }
            
            $data = [
                'title' => trim($_POST['title'] ?? ''),
                'content' => trim($_POST['content'] ?? ''),
                'category' => $_POST['category'] ?? 'general',
                'date' => $_POST['date'] ?? date('Y-m-d'),
                'status' => $_POST['edit_status'] ?? 'draft',
                'image_url' => $imageUrl
            ];
            
            echo "<strong>Update Data:</strong><pre>" . htmlspecialchars(print_r($data, true)) . "</pre>";
            
            try {
                $result = Announcement::update($announcementId, $data);
                echo "<strong>Update Result:</strong> " . ($result ? 'SUCCESS' : 'FAILED') . "<br>";
                
                if ($result) {
                    $updated = Announcement::get($announcementId);
                    echo "<strong>Verification - Updated Record:</strong><pre>" . htmlspecialchars(print_r($updated, true)) . "</pre>";
                    $notice = 'Announcement updated successfully!';
                    $announcement = $updated; // Refresh the form data
                } else {
                    $notice = 'Failed to update announcement!';
                }
            } catch (Exception $e) {
                echo "<strong>Exception:</strong> " . htmlspecialchars($e->getMessage()) . "<br>";
                $notice = 'Error: ' . $e->getMessage();
            }
        }
        echo "</div>";
    }
    
    if ($notice) {
        $class = strpos($notice, 'success') !== false ? 'success' : 'error';
        echo "<div class='result $class'>$notice</div>";
    }
    ?>
    
    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="edit_announcement" value="1">
        <input type="hidden" name="id" value="<?= htmlspecialchars($announcement['id'] ?? '1') ?>">
        <input type="hidden" name="current_image_url" value="<?= htmlspecialchars($announcement['image_url'] ?? '') ?>">
        
        <div class="form-group">
            <label>Title:</label>
            <input type="text" name="title" value="<?= htmlspecialchars($announcement['title'] ?? '') ?>" required>
        </div>
        
        <div class="form-group">
            <label>Content:</label>
            <textarea name="content" rows="5" required><?= htmlspecialchars($announcement['body'] ?? '') ?></textarea>
        </div>
        
        <div class="form-group">
            <label>Category:</label>
            <select name="category">
                <option value="general" <?= ($announcement['category'] ?? '') === 'general' ? 'selected' : '' ?>>General</option>
                <option value="advisory" <?= ($announcement['category'] ?? '') === 'advisory' ? 'selected' : '' ?>>Advisory</option>
                <option value="deadline" <?= ($announcement['category'] ?? '') === 'deadline' ? 'selected' : '' ?>>Deadline</option>
                <option value="policy" <?= ($announcement['category'] ?? '') === 'policy' ? 'selected' : '' ?>>Policy</option>
                <option value="alert" <?= ($announcement['category'] ?? '') === 'alert' ? 'selected' : '' ?>>Alert</option>
            </select>
        </div>
        
        <div class="form-group">
            <label>Date:</label>
            <input type="date" name="date" value="<?= htmlspecialchars($announcement['date'] ?? date('Y-m-d')) ?>">
        </div>
        
        <div class="form-group">
            <label>Status:</label>
            <select name="edit_status">
                <option value="draft" <?= ($announcement['status'] ?? '') === 'draft' ? 'selected' : '' ?>>Draft</option>
                <option value="published" <?= ($announcement['status'] ?? '') === 'published' ? 'selected' : '' ?>>Published</option>
                <option value="archived" <?= ($announcement['status'] ?? '') === 'archived' ? 'selected' : '' ?>>Archived</option>
            </select>
        </div>
        
        <div class="form-group">
            <label>Current Image:</label>
            <?php if (!empty($announcement['image_url'])): ?>
                <img src="<?= htmlspecialchars($announcement['image_url']) ?>" style="max-width: 200px; max-height: 150px; display: block; margin: 10px 0;">
                <label><input type="checkbox" name="remove_image" value="1"> Remove current image</label>
            <?php else: ?>
                <p>No image currently set</p>
            <?php endif; ?>
        </div>
        
        <div class="form-group">
            <label>New Image:</label>
            <input type="file" name="image" accept="image/*">
        </div>
        
        <button type="submit">Update Announcement</button>
    </form>
    
    <div style="margin-top: 30px;">
        <h3>Current Announcement Data:</h3>
        <pre><?= htmlspecialchars(print_r($announcement, true)) ?></pre>
    </div>
</body>
</html>