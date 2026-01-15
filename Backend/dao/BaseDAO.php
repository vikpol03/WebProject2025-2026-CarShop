<?php
// lib/BaseDAO.php
declare(strict_types=1);

abstract class BaseDAO {
    protected PDO $pdo;
    protected string $table;
    protected string $pk;
    /** @var string[] */
    protected array $fillable; // columns allowed in create/update

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
        [$this->table, $this->pk, $this->fillable] = $this->meta();
    }

    /**
     * Return [table, pk, fillableColumns]
     * @return array{0:string,1:string,2:array}
     */
    abstract protected function meta(): array;

    public function findById(int $id): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM `{$this->table}` WHERE `{$this->pk}` = :id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Simple list with pagination + optional where (safe column filters only).
     * $filters: ['col' => value] (ANDed equality)
     */
    public function list(int $limit = 50, int $offset = 0, array $filters = []): array {
        $where = [];
        $params = [];
        foreach ($filters as $col => $val) {
            // only allow known columns
            if ($col === $this->pk || in_array($col, $this->fillable, true)) {
                $p = ":f_" . $col;
                $where[] = "`$col` = $p";
                $params[$p] = $val;
            }
        }
        $whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';
        $sql = "SELECT * FROM `{$this->table}` $whereSql ORDER BY `{$this->pk}` DESC LIMIT :limit OFFSET :offset";
        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $p => $v) $stmt->bindValue($p, $v);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Inserts row, returns inserted ID.
     */
    public function create(array $data): int {
        $payload = array_intersect_key($data, array_flip($this->fillable));
        if (!$payload) throw new InvalidArgumentException("No valid fields to insert.");

        $cols = array_keys($payload);
        $placeholders = array_map(fn($c) => ":$c", $cols);
        $sql = "INSERT INTO `{$this->table}` (" . implode(',', array_map(fn($c) => "`$c`", $cols)) . ")
                VALUES (" . implode(',', $placeholders) . ")";
        $stmt = $this->pdo->prepare($sql);
        foreach ($payload as $k => $v) $stmt->bindValue(":$k", $v);
        $stmt->execute();
        return (int)$this->pdo->lastInsertId();
    }

    /**
     * Updates row by ID, returns bool.
     */
    public function update(int $id, array $data): bool {
        $payload = array_intersect_key($data, array_flip($this->fillable));
        if (!$payload) return false;
        $sets = [];
        foreach (array_keys($payload) as $c) $sets[] = "`$c` = :$c";
        $sql = "UPDATE `{$this->table}` SET " . implode(',', $sets) . " WHERE `{$this->pk}` = :id";
        $stmt = $this->pdo->prepare($sql);
        foreach ($payload as $k => $v) $stmt->bindValue(":$k", $v);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function delete(int $id): bool {
        $stmt = $this->pdo->prepare("DELETE FROM `{$this->table}` WHERE `{$this->pk}` = :id");
        return $stmt->execute([':id' => $id]);
    }
}
