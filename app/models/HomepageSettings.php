<?php
declare(strict_types=1);
require_once __DIR__ . '/Model.php';
final class HomepageSettings extends Model {
  public function get(): array {
    $row = self::db()->query("SELECT * FROM homepage_settings WHERE id=1")->fetch();
    return $row ?: [];
  }
  public function update(array $d): bool {
    $sql = "UPDATE homepage_settings SET
      hero_eyebrow=:hero_eyebrow,
      hero_title=:hero_title, hero_subtitle=:hero_subtitle, hero_bg=:hero_bg,
      btn_primary_text=:bpt, btn_primary_url=:bpu, btn_secondary_text=:bst, btn_secondary_url=:bsu,
      why_title=:why_title, why_subtitle=:why_subtitle,
      why_faculty_text=:wft, why_faculty_desc=:wfd, why_faculty_link_label=:wfll, why_faculty_link=:wfl,
      why_facilities_text=:wfat, why_facilities_desc=:wfad, why_facilities_link_label=:wfall, why_facilities_link=:wfal,
      why_career_text=:wct, why_career_desc=:wcd, why_career_link_label=:wcll, why_career_link=:wcl,
      spotlight_eyebrow=:spe, spotlight_title=:spt, spotlight_blurb=:spb, spotlight_image=:spi, spotlight_image_alt=:spia,
      spotlight_cta_text=:spct, spotlight_cta_url=:spcu, spotlight_cta2_text=:spct2, spotlight_cta2_url=:spcu2, spotlight_video_url=:spvu,
      cta_title=:cta_title, cta_description=:cta_desc, cta_action_label=:cta_label, cta_action_url=:cta_url,
      programs_undergrad_blurb=:pub, programs_dual_blurb=:pdb, programs_grad_blurb=:pgb,
      show_pinned_announcements=:spa
      WHERE id=1";
    $st = self::db()->prepare($sql);
    return $st->execute([
      ':hero_eyebrow'=>$d['hero_eyebrow'] ?? null,
      ':hero_title'=>$d['hero_title'] ?? null,
      ':hero_subtitle'=>$d['hero_subtitle'] ?? null,
      ':hero_bg'=>$d['hero_bg'] ?? null,
      ':bpt'=>$d['btn_primary_text'] ?? null, ':bpu'=>$d['btn_primary_url'] ?? null,
      ':bst'=>$d['btn_secondary_text'] ?? null, ':bsu'=>$d['btn_secondary_url'] ?? null,
      ':why_title'=>$d['why_title'] ?? null,
      ':why_subtitle'=>$d['why_subtitle'] ?? null,
      ':wft'=>$d['why_faculty_text'] ?? null, ':wfd'=>$d['why_faculty_desc'] ?? null, ':wfll'=>$d['why_faculty_link_label'] ?? null, ':wfl'=>$d['why_faculty_link'] ?? null,
      ':wfat'=>$d['why_facilities_text'] ?? null, ':wfad'=>$d['why_facilities_desc'] ?? null, ':wfall'=>$d['why_facilities_link_label'] ?? null, ':wfal'=>$d['why_facilities_link'] ?? null,
      ':wct'=>$d['why_career_text'] ?? null, ':wcd'=>$d['why_career_desc'] ?? null, ':wcll'=>$d['why_career_link_label'] ?? null, ':wcl'=>$d['why_career_link'] ?? null,
      ':spe'=>$d['spotlight_eyebrow'] ?? null, ':spt'=>$d['spotlight_title'] ?? null, ':spb'=>$d['spotlight_blurb'] ?? null, ':spi'=>$d['spotlight_image'] ?? null, ':spia'=>$d['spotlight_image_alt'] ?? null,
      ':spct'=>$d['spotlight_cta_text'] ?? null, ':spcu'=>$d['spotlight_cta_url'] ?? null, ':spct2'=>$d['spotlight_cta2_text'] ?? null, ':spcu2'=>$d['spotlight_cta2_url'] ?? null, ':spvu'=>$d['spotlight_video_url'] ?? null,
      ':cta_title'=>$d['cta_title'] ?? null, ':cta_desc'=>$d['cta_description'] ?? null, ':cta_label'=>$d['cta_action_label'] ?? null, ':cta_url'=>$d['cta_action_url'] ?? null,
      ':pub'=>$d['programs_undergrad_blurb'] ?? null, ':pdb'=>$d['programs_dual_blurb'] ?? null, ':pgb'=>$d['programs_grad_blurb'] ?? null,
      ':spa'=>!empty($d['show_pinned_announcements']) ? 1 : 0,
    ]);
  }
}
