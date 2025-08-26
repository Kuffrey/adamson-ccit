<?php
declare(strict_types=1);
final class Partner extends Model {
  public function listActive(): array {
    $st = $this->db->query("SELECT * FROM partners WHERE is_active=1 ORDER BY sort_order, name");
    return $st->fetchAll();
  }
  public function create(array $d): int {
    $st=$this->db->prepare("INSERT INTO partners (name,slug,logo_path,short_desc,website_url,sort_order,is_active)
      VALUES (:n,:s,:l,:d,:w,:o,:a)");
    $st->execute([
      ':n'=>$d['name'], ':s'=>$d['slug'], ':l'=>$d['logo_path'], ':d'=>$d['short_desc']??null,
      ':w'=>$d['website_url']??null, ':o'=>(int)($d['sort_order']??0), ':a'=>!empty($d['is_active'])?1:0
    ]);
    return (int)$this->db->lastInsertId();
  }
  public function update(int $id, array $d): bool {
    $st=$this->db->prepare("UPDATE partners SET name=:n,slug=:s,logo_path=:l,short_desc=:d,website_url=:w,sort_order=:o,is_active=:a WHERE id=:id");
    return $st->execute([
      ':n'=>$d['name'], ':s'=>$d['slug'], ':l'=>$d['logo_path'], ':d'=>$d['short_desc']??null,
      ':w'=>$d['website_url']??null, ':o'=>(int)($d['sort_order']??0), ':a'=>!empty($d['is_active'])?1:0, ':id'=>$id
    ]);
  }
  public function find(int $id): ?array {
    $st=$this->db->prepare("SELECT * FROM partners WHERE id=:id"); $st->execute([':id'=>$id]); $r=$st->fetch(); return $r?:null;
  }
  public function delete(int $id): bool {
    return $this->db->prepare("DELETE FROM partners WHERE id=:id")->execute([':id'=>$id]);
  }
}
