<?php
// app/models/AboutVisionMission.php
require_once __DIR__ . '/Model.php';
final class AboutVisionMission extends Model {
  public function get(): array {
    $row = self::db()->query("SELECT * FROM about_vision_mission ORDER BY id DESC LIMIT 1")->fetch();
    return $row ?: [];
  }
  public function update(array $d): bool {
    $sql = "UPDATE about_vision_mission SET
      main_vision=:main_vision,
      main_mission=:main_mission,
      main_intro=:main_intro,
      dept1_title=:dept1_title,
      dept1_vision=:dept1_vision,
      dept1_mission=:dept1_mission,
      dept1_objectives=:dept1_objectives,
      dept2_title=:dept2_title,
      dept2_vision=:dept2_vision,
      dept2_mission=:dept2_mission,
      dept2_objectives=:dept2_objectives
      WHERE id=1";
    $st = self::db()->prepare($sql);
    return $st->execute([
      ':main_vision'=>$d['main_vision'] ?? null,
      ':main_mission'=>$d['main_mission'] ?? null,
      ':main_intro'=>$d['main_intro'] ?? null,
      ':dept1_title'=>$d['dept1_title'] ?? null,
      ':dept1_vision'=>$d['dept1_vision'] ?? null,
      ':dept1_mission'=>$d['dept1_mission'] ?? null,
      ':dept1_objectives'=>$d['dept1_objectives'] ?? null,
      ':dept2_title'=>$d['dept2_title'] ?? null,
      ':dept2_vision'=>$d['dept2_vision'] ?? null,
      ':dept2_mission'=>$d['dept2_mission'] ?? null,
      ':dept2_objectives'=>$d['dept2_objectives'] ?? null,
    ]);
  }
}
