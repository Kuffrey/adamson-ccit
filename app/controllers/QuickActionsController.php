<?php
declare(strict_types=1);

final class QuickActionsController
{
    private function requireModels(): void
    {
        require_once __DIR__ . '/../models/Model.php';
        require_once __DIR__ . '/../models/QuickAction.php';
    }

    /**
     * Display quick actions management page
     */
    public function index(): string
    {
        $this->requireModels();

        $success = $_SESSION['quick_actions_success'] ?? null;
        $error = $_SESSION['quick_actions_error'] ?? null;
        unset($_SESSION['quick_actions_success'], $_SESSION['quick_actions_error']);

        $quickActions = QuickAction::getAll();

        ob_start();
        include __DIR__ . '/../views/admin_manage_quick_actions.php';
        return ob_get_clean();
    }

    /**
     * Handle form submissions
     */
    public function save(): string
    {
        $this->requireModels();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('error', 'Invalid request method.');
            return $this->index();
        }

        $action = $_POST['action'] ?? '';

        try {
            switch ($action) {
                case 'create':
                    $this->handleCreate();
                    break;
                case 'update':
                    $this->handleUpdate();
                    break;
                case 'delete':
                    $this->handleDelete();
                    break;
                case 'toggle':
                    $this->handleToggle();
                    break;
                case 'reorder':
                    $this->handleReorder();
                    break;
                default:
                    $this->redirect('error', 'Unknown action.');
                    break;
            }
        } catch (\Throwable $e) {
            $this->redirect('error', 'Operation failed: ' . $e->getMessage());
        }

        return $this->index();
    }

    private function handleCreate(): void
    {
        $data = [
            'label' => trim($_POST['label'] ?? ''),
            'icon' => trim($_POST['icon'] ?? ''),
            'url' => trim($_POST['url'] ?? ''),
            'display_order' => (int)($_POST['display_order'] ?? 0),
            'is_active' => !empty($_POST['is_active']),
        ];

        if (empty($data['label']) || empty($data['url'])) {
            $this->redirect('error', 'Label and URL are required.');
            return;
        }

        if (QuickAction::create($data)) {
            $this->redirect('success', 'Quick action created successfully.');
        } else {
            $this->redirect('error', 'Failed to create quick action.');
        }
    }

    private function handleUpdate(): void
    {
        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            $this->redirect('error', 'Invalid quick action ID.');
            return;
        }

        $data = [
            'label' => trim($_POST['label'] ?? ''),
            'icon' => trim($_POST['icon'] ?? ''),
            'url' => trim($_POST['url'] ?? ''),
            'display_order' => (int)($_POST['display_order'] ?? 0),
            'is_active' => !empty($_POST['is_active']),
        ];

        if (empty($data['label']) || empty($data['url'])) {
            $this->redirect('error', 'Label and URL are required.');
            return;
        }

        if (QuickAction::update($id, $data)) {
            $this->redirect('success', 'Quick action updated successfully.');
        } else {
            $this->redirect('error', 'Failed to update quick action.');
        }
    }

    private function handleDelete(): void
    {
        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            $this->redirect('error', 'Invalid quick action ID.');
            return;
        }

        if (QuickAction::delete($id)) {
            $this->redirect('success', 'Quick action deleted successfully.');
        } else {
            $this->redirect('error', 'Failed to delete quick action.');
        }
    }

    private function handleToggle(): void
    {
        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            $this->redirect('error', 'Invalid quick action ID.');
            return;
        }

        if (QuickAction::toggleActive($id)) {
            $this->redirect('success', 'Quick action status updated.');
        } else {
            $this->redirect('error', 'Failed to update quick action status.');
        }
    }

    private function handleReorder(): void
    {
        $orderData = $_POST['order'] ?? [];
        if (!is_array($orderData) || empty($orderData)) {
            $this->redirect('error', 'Invalid order data.');
            return;
        }

        if (QuickAction::updateOrder($orderData)) {
            $this->redirect('success', 'Quick actions reordered successfully.');
        } else {
            $this->redirect('error', 'Failed to reorder quick actions.');
        }
    }

    private function redirect(string $type, string $message): void
    {
        $_SESSION["quick_actions_{$type}"] = $message;
        header('Location: ?page=admin_manage_quick_actions');
        exit;
    }
}