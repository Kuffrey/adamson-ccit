<?php
// app/models/EventsPageSettings.php
require_once __DIR__ . '/Model.php';
final class EventsPageSettings extends Model {
  public function get(): array {
    $row = self::db()->query("SELECT * FROM events_page_settings ORDER BY id DESC LIMIT 1")->fetch();
    return $row ?: [];
  }
  public function update(array $d): bool {
    $sql = "UPDATE events_page_settings SET
      subhero_lead=:subhero_lead,
      intro_title=:intro_title,
      intro_lead=:intro_lead,
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
      ':cta_title'=>$d['cta_title'] ?? null,
      ':cta_body'=>$d['cta_body'] ?? null,
      ':cta_btn_label'=>$d['cta_btn_label'] ?? null,
      ':cta_btn_url'=>$d['cta_btn_url'] ?? null,
    ]);
  }
}
