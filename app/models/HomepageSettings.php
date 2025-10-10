<?php
declare(strict_types=1);
require_once __DIR__ . '/Model.php';
final class HomepageSettings extends Model {
  public function get(): array {
    $row = self::db()->query("SELECT * FROM homepage_settings WHERE id=1")->fetch();
    return $row ?: [];
  }

  public function update(array $d): bool {
    // Map checkbox to int
    $d['show_pinned_announcements'] = !empty($d['show_pinned_announcements']) ? 1 : 0;

    // Whitelist allowable columns (must match DB)
    $fields = [
      'hero_eyebrow','hero_title','hero_subtitle','hero_bg',
      'btn_primary_text','btn_primary_url','btn_secondary_text','btn_secondary_url',
      'why_title','why_subtitle',
      'why_faculty_text','why_faculty_desc','why_faculty_link_label','why_faculty_link','why_faculty_image',
      'why_facilities_text','why_facilities_desc','why_facilities_link_label','why_facilities_link','why_facilities_image',
      'why_career_text','why_career_desc','why_career_link_label','why_career_link','why_career_image',
      'spotlight_eyebrow','spotlight_title','spotlight_blurb','spotlight_image','spotlight_image_alt',
      'spotlight_cta_text','spotlight_cta_url','spotlight_cta2_text','spotlight_cta2_url','spotlight_video_url',
      'cta_title','cta_description','cta_action_label','cta_action_url',
      'programs_undergrad_blurb','programs_dual_blurb','programs_grad_blurb',
      'show_pinned_announcements'
    ];

    // Keep only keys that are present in $d (prevents wiping others)
    $data = array_intersect_key($d, array_flip($fields));
    if (!$data) return true; // nothing to update

    // Build dynamic SQL
    $sets = [];
    $params = [':id' => 1];
    foreach ($data as $k => $v) {
      $sets[] = "`$k` = :$k";
      $params[":$k"] = $v;
    }
    $sql = "UPDATE homepage_settings SET ".implode(', ', $sets)." WHERE id=:id";
    $st = self::db()->prepare($sql);
    return $st->execute($params);
  }
}
