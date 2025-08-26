<?php
declare(strict_types=1);
final class HomepageSettings extends Model {
  public function get(): array {
    $row = $this->db->query("SELECT * FROM homepage_settings WHERE id=1")->fetch();
    return $row ?: [];
  }
  public function update(array $d): bool {
    $sql = "UPDATE homepage_settings SET
      hero_title=:hero_title, hero_subtitle=:hero_subtitle, hero_bg=:hero_bg,
      btn_primary_text=:bpt, btn_primary_url=:bpu, btn_secondary_text=:bst, btn_secondary_url=:bsu,
      why_faculty_text=:wft, why_faculty_link=:wfl,
      why_facilities_text=:wfat, why_facilities_link=:wfal,
      why_career_text=:wct, why_career_link=:wcl,
      spotlight_title=:spt, spotlight_blurb=:spb, spotlight_image=:spi, spotlight_cta_text=:spct,
      spotlight_cta_url=:spcu, spotlight_video_url=:spvu,
      programs_undergrad_blurb=:pub, programs_dual_blurb=:pdb, programs_grad_blurb=:pgb,
      show_pinned_announcements=:spa
      WHERE id=1";
    $st = $this->db->prepare($sql);
    return $st->execute([
      ':hero_title'=>$d['hero_title'] ?? '',
      ':hero_subtitle'=>$d['hero_subtitle'] ?? null,
      ':hero_bg'=>$d['hero_bg'] ?? null,
      ':bpt'=>$d['btn_primary_text'] ?? null, ':bpu'=>$d['btn_primary_url'] ?? null,
      ':bst'=>$d['btn_secondary_text'] ?? null, ':bsu'=>$d['btn_secondary_url'] ?? null,
      ':wft'=>$d['why_faculty_text'] ?? null, ':wfl'=>$d['why_faculty_link'] ?? null,
      ':wfat'=>$d['why_facilities_text'] ?? null, ':wfal'=>$d['why_facilities_link'] ?? null,
      ':wct'=>$d['why_career_text'] ?? null, ':wcl'=>$d['why_career_link'] ?? null,
      ':spt'=>$d['spotlight_title'] ?? null, ':spb'=>$d['spotlight_blurb'] ?? null, ':spi'=>$d['spotlight_image'] ?? null,
      ':spct'=>$d['spotlight_cta_text'] ?? null, ':spcu'=>$d['spotlight_cta_url'] ?? null, ':spvu'=>$d['spotlight_video_url'] ?? null,
      ':pub'=>$d['programs_undergrad_blurb'] ?? null, ':pdb'=>$d['programs_dual_blurb'] ?? null, ':pgb'=>$d['programs_grad_blurb'] ?? null,
      ':spa'=>!empty($d['show_pinned_announcements']) ? 1 : 0,
    ]);
  }
}
