<?php
// app/models/HeaderRepository.php

class HeaderRepository
{
    public static function getSettings(PDO $pdo): array {
        $row = $pdo->query("SELECT * FROM header_settings ORDER BY id DESC LIMIT 1")->fetch();
        return $row ?: [
            'logo_url' => '/adamson-ccit/public/assets/images/adamson-ccit-logo.png',
            'cta_label' => 'Career Pathway Generator',
            'cta_url' => '/adamson-ccit/public/index.php?page=career_pathway_generator',
        ];
    }

    public static function getUtilityLinks(PDO $pdo): array {
        $stmt = $pdo->query("SELECT label, url, is_external FROM header_utility_links ORDER BY sort_order ASC, id ASC");
        return $stmt ? $stmt->fetchAll() : [];
    }

    /** Returns an array of top-level menu with possible 'items' children */
    public static function getMenu(PDO $pdo): array {
        $tops = $pdo->query("SELECT id, label, url, type FROM header_menu ORDER BY sort_order ASC, id ASC")->fetchAll();
        if (!$tops) return [];

        // Get children for dropdowns
        $dropIds = array_column(array_filter($tops, fn($m) => $m['type']==='dropdown'), 'id');
        $children = [];
        if ($dropIds) {
            $in = implode(',', array_map('intval', $dropIds));
            $rows = $pdo->query("SELECT menu_id, label, url FROM header_menu_items WHERE menu_id IN ($in) ORDER BY sort_order ASC, id ASC")->fetchAll();
            foreach ($rows as $r) { $children[$r['menu_id']][] = $r; }
        }

        // Attach
        foreach ($tops as &$m) {
            if ($m['type'] === 'dropdown') {
                $m['items'] = $children[$m['id']] ?? [];
            }
        }
        return $tops;
    }
}
