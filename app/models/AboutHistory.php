<?php
// app/models/AboutHistory.php
require_once __DIR__ . '/Model.php';
final class AboutHistory extends Model {
  public function get(): array {
    $row = self::db()->query("SELECT * FROM about_history ORDER BY id DESC LIMIT 1")->fetch();
    if ($row && !empty($row['milestones'])) {
      $row['milestones'] = json_decode($row['milestones'], true);
    }
    return $row ?: [];
  }
  public function update(array $d): bool {
    $sql = "UPDATE about_history SET
      subhero_lead=:subhero_lead,
      intro_title=:intro_title,
      intro_lead=:intro_lead,
      fact_title=:fact_title,
      fact_1=:fact_1,
      fact_2=:fact_2,
      fact_3=:fact_3,
      origins_title=:origins_title,
      origins_body=:origins_body,
      milestones=:milestones,
      leaders_title=:leaders_title,
      leaders_list=:leaders_list,
      academic_leads_title=:academic_leads_title,
      academic_leads_list=:academic_leads_list,
      identity_title=:identity_title,
      identity_items=:identity_items,
      cta_title=:cta_title,
      cta_body=:cta_body,
      cta_btn_label=:cta_btn_label,
      cta_btn_url=:cta_btn_url
      WHERE id=1";
    $st = self::db()->prepare($sql);
    return $st->execute([
      ':subhero_lead'=>$d['subhero_lead'] ?? null,
      ':intro_title'=>$d['intro_title'] ?? null,
      ':intro_lead'=>$d['intro_lead'] ?? null,
      ':fact_title'=>$d['fact_title'] ?? null,
      ':fact_1'=>$d['fact_1'] ?? null,
      ':fact_2'=>$d['fact_2'] ?? null,
      ':fact_3'=>$d['fact_3'] ?? null,
      ':origins_title'=>$d['origins_title'] ?? null,
      ':origins_body'=>$d['origins_body'] ?? null,
      ':milestones'=>json_encode($d['milestones'] ?? []),
      ':leaders_title'=>$d['leaders_title'] ?? null,
      ':leaders_list'=>$d['leaders_list'] ?? null,
      ':academic_leads_title'=>$d['academic_leads_title'] ?? null,
      ':academic_leads_list'=>$d['academic_leads_list'] ?? null,
      ':identity_title'=>$d['identity_title'] ?? null,
      ':identity_items'=>$d['identity_items'] ?? null,
      ':cta_title'=>$d['cta_title'] ?? null,
      ':cta_body'=>$d['cta_body'] ?? null,
      ':cta_btn_label'=>$d['cta_btn_label'] ?? null,
      ':cta_btn_url'=>$d['cta_btn_url'] ?? null,
    ]);
  }
}
