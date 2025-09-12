<?php
// app/models/FooterRepository.php

class FooterRepository {
    /** Return assoc settings row */
    public static function getSettings(PDO $pdo): array {
        $sql = "SELECT * FROM footer_settings ORDER BY id DESC LIMIT 1";
        $stmt = $pdo->query($sql);
        $row = $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : null;

        // sensible defaults if empty
        if (!$row) {
            $row = [
                'org_name'       => 'Adamson University',
                'college_name'   => 'College of Computing and Information Technology',
                'addr_line1'     => '900 San Marcelino Street, Ermita',
                'addr_line2'     => '1000 Manila, Philippines',
                'phone_display'  => '(02) 8524-20-11 loc. 324',
                'phone_tel'      => '+63285242011',
                'email'          => 'ccit@adamson.edu.ph',
                'website_label'  => 'https://www.adamson.edu.ph/cfe/',
                'website_url'    => 'https://www.adamson.edu.ph/cfe/',
                'legal_note'     => 'All Rights Reserved',
                'powered_by'     => 'Powered by Information Technology Center',
                'trademark_note' => 'Trademark Notice',
                'sitemap_label'  => 'Site Map',
                'sitemap_url'    => '#',
            ];
        }
        return $row;
    }

    /** Quick links only */
    public static function getQuickLinks(PDO $pdo): array {
        $sql = "SELECT label, url, is_external FROM footer_links
                WHERE kind='quick' ORDER BY sort_order ASC, id ASC";
        $stmt = $pdo->query($sql);
        return $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    }

    /** Legal links (optional, if you want to list them from DB) */
    public static function getLegalLinks(PDO $pdo): array {
        $sql = "SELECT label, url, is_external FROM footer_links
                WHERE kind='legal' ORDER BY sort_order ASC, id ASC";
        $stmt = $pdo->query($sql);
        return $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    }

    /** Socials */
    public static function getSocials(PDO $pdo): array {
        $sql = "SELECT platform, label, url FROM footer_socials
                WHERE is_enabled=1 ORDER BY sort_order ASC, id ASC";
        $stmt = $pdo->query($sql);
        return $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    }
}
