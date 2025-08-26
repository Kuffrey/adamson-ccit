<?php
declare(strict_types=1);

final class Department extends Model {
    public function all(): array {
        return $this->db->query("SELECT id, name FROM departments ORDER BY name")->fetchAll();
    }
}
