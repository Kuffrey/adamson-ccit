<?php
// app/models/AboutVisionMission.php
require_once __DIR__ . '/Model.php';

final class AboutVisionMission extends Model {
  public function get(): array {
    $row = self::db()->query("SELECT * FROM about_vision_mission ORDER BY id DESC LIMIT 1")->fetch();
    return $row ?: [];
  }

  /** Insert-or-update in one query (requires `id` to be PRIMARY/UNIQUE). */
  public function update(array $d): bool {
    $sql = "INSERT INTO about_vision_mission
      (id, main_vision, main_mission, main_intro,
       dept1_title, dept1_vision, dept1_mission, dept1_objectives,
       dept2_title, dept2_vision, dept2_mission, dept2_objectives)
      VALUES
      (1, :main_vision, :main_mission, :main_intro,
          :dept1_title, :dept1_vision, :dept1_mission, :dept1_objectives,
          :dept2_title, :dept2_vision, :dept2_mission, :dept2_objectives)
      ON DUPLICATE KEY UPDATE
        main_vision=VALUES(main_vision),
        main_mission=VALUES(main_mission),
        main_intro=VALUES(main_intro),
        dept1_title=VALUES(dept1_title),
        dept1_vision=VALUES(dept1_vision),
        dept1_mission=VALUES(dept1_mission),
        dept1_objectives=VALUES(dept1_objectives),
        dept2_title=VALUES(dept2_title),
        dept2_vision=VALUES(dept2_vision),
        dept2_mission=VALUES(dept2_mission),
        dept2_objectives=VALUES(dept2_objectives)";
    $st = self::db()->prepare($sql);
    return $st->execute([
      ':main_vision'       => $d['main_vision']        ?? '',
      ':main_mission'      => $d['main_mission']       ?? '',
      ':main_intro'        => $d['main_intro']         ?? '',
      ':dept1_title'       => $d['dept1_title']        ?? '',
      ':dept1_vision'      => $d['dept1_vision']       ?? '',
      ':dept1_mission'     => $d['dept1_mission']      ?? '',
      ':dept1_objectives'  => $d['dept1_objectives']   ?? '',
      ':dept2_title'       => $d['dept2_title']        ?? '',
      ':dept2_vision'      => $d['dept2_vision']       ?? '',
      ':dept2_mission'     => $d['dept2_mission']      ?? '',
      ':dept2_objectives'  => $d['dept2_objectives']   ?? '',
    ]);
  }
}
