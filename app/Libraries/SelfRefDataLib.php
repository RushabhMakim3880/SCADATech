<?php

namespace App\Libraries;

use CodeIgniter\Database\BaseConnection;

class SelfRefDataLib
{
    protected BaseConnection $db;
    protected string $tableName;
    protected string $primaryField;
    protected string $nameField;
    protected $parentIdField;
    protected string $whereCondition;

    public function __construct(string $tableName, string $nameField = 'name', $parentIdField = null, string $whereCondition = '1')
    {
        $this->db = db_connect();

        $this->tableName = $tableName;
        $this->nameField = $nameField;
        $this->parentIdField = $parentIdField;
        $this->whereCondition = $whereCondition;

        // Auto detect primary key
        $fields = $this->db->getFieldData($tableName);
        foreach ($fields as $field) {
            if ($field->primary_key == 1) {
                $this->primaryField = $field->name;
                break;
            }
        }
    }

    public function search(string $query = '', int $limit = 10, int $offset = 0): array
    {
        if ($this->parentIdField == null) {
            $sql = "SELECT 
                    {$this->primaryField} AS id, 
                    {$this->nameField} AS text
                FROM {$this->tableName} 
                WHERE {$this->nameField} LIKE :search: AND $this->whereCondition
                ORDER BY {$this->nameField}
                LIMIT $offset, $limit";
        } else {
            $sql = "WITH RECURSIVE cte_hierarchy AS (
                SELECT 
                        t.*,
                        CAST(t.{$this->nameField} AS CHAR(500)) AS name,
                        CAST(NULL AS CHAR(1000)) AS parents
                    FROM {$this->tableName} t 
                    WHERE t.{$this->parentIdField} IS NULL

                    UNION ALL

                    SELECT 
                        t.*,
                        CONCAT(p.name, ' → ', t.{$this->nameField}) AS name,
                        CASE
                            WHEN p.parents IS NULL THEN p.{$this->nameField}
                            ELSE CONCAT(ltrim(p.{$this->nameField}), ', ', p.parents)
                        END AS parents
                    FROM {$this->tableName} t 
                    INNER JOIN cte_hierarchy p ON t.{$this->parentIdField} = p.{$this->primaryField}
                )
                SELECT 
                    {$this->primaryField} AS id, 
                    {$this->nameField} AS text, 
                    parents as subtext
                FROM cte_hierarchy 
                WHERE name LIKE :search: AND $this->whereCondition
                ORDER BY name
                LIMIT $offset, $limit";
        }



        $searchResult =  $this->db->query($sql, ['search' => "%{$query}%"])->getResultArray();

        return $searchResult;
    }

    public function getAllWithParent(): array
    {
        $sql = "WITH RECURSIVE cte_hierarchy AS (
                SELECT 
                        t.*,
                        CAST(t.{$this->nameField} AS CHAR(500)) AS name,
                        CAST(NULL AS CHAR(1000)) AS parents
                    FROM {$this->tableName} t 
                    WHERE t.{$this->parentIdField} IS NULL

                    UNION ALL

                    SELECT 
                        t.*,
                        CONCAT(p.name, ' → ', t.{$this->nameField}) AS name,
                        CASE
                            WHEN p.parents IS NULL THEN p.{$this->nameField}
                            ELSE CONCAT(ltrim(p.{$this->nameField}), ', ', p.parents)
                        END AS parents
                    FROM {$this->tableName} t 
                    INNER JOIN cte_hierarchy p ON t.{$this->parentIdField} = p.{$this->primaryField}
                )
                SELECT 
                    {$this->primaryField} AS id, 
                    {$this->nameField} AS name, 
                    parents as subtext
                FROM cte_hierarchy 
                WHERE $this->whereCondition
                ORDER BY name";

        $searchResult =  $this->db->query($sql)->getResultArray();

        foreach ($searchResult as $key => $row) {
            $searchResult[$key]['name'] = $searchResult[$key]['name'] . "||" . $row['subtext'];
            // $searchResult[$key]['attributes']['subtext'] = $row['subtext'];
            unset($searchResult[$key]['subtext']);
        }

        return $searchResult;
    }

    public function searchCount(string $query = ''): int
    {
        if ($this->parentIdField == null) {
            $totalSql = "SELECT count(*) as total
                FROM {$this->tableName} 
                WHERE {$this->nameField} LIKE :search: AND $this->whereCondition
                ORDER BY {$this->nameField}";
        } else {
            $totalSql = "WITH RECURSIVE cte_hierarchy AS (
                SELECT 
                        t.*,
                        CAST(t.{$this->nameField} AS CHAR(500)) AS name,
                        CAST(NULL AS CHAR(1000)) AS parents
                    FROM {$this->tableName} t 
                    WHERE t.{$this->parentIdField} IS NULL

                    UNION ALL

                    SELECT 
                        t.*,
                        CONCAT(p.name, ' → ', t.{$this->nameField}) AS name,
                        CASE
                            WHEN p.parents IS NULL THEN p.{$this->nameField}
                            ELSE CONCAT(ltrim(p.{$this->nameField}), ', ', p.parents)
                        END AS parents
                    FROM {$this->tableName} t 
                    INNER JOIN cte_hierarchy p ON t.{$this->parentIdField} = p.{$this->primaryField}
                )
                SELECT count(*) as total
                FROM cte_hierarchy 
                WHERE name LIKE :search: AND $this->whereCondition
                ORDER BY name";
        }



        $total = $this->db->query($totalSql, ['search' => "%{$query}%"])->getRow()->total;

        return $total;
    }

    public function getNameById($id): ?string
    {
        return $this->db->table($this->tableName)
            ->select($this->nameField)
            ->where($this->primaryField, $id)
            ->get()
            ->getRow($this->nameField);
    }

    public function getFullPathByIdReverse($id): string
    {
        $path = $this->getFullPathById($id);
        return implode(', ', array_reverse(explode(', ', $path)));
    }

    public function getFullPathById($id): string
    {
        $names = [];
        while ($id) {
            $row = $this->db->table($this->tableName)
                ->select("{$this->nameField}, {$this->parentIdField}")
                ->where($this->primaryField, $id)
                ->get()
                ->getRowArray();
            if (!$row) break;
            $names[] = $row[$this->nameField];
            $id = $row[$this->parentIdField];
        }
        return implode(', ', $names);
    }

    // Extra Utility: get children recursively
    public function getChildren($parentId = null): array
    {
        $results = [];
        $this->fetchChildren($parentId, $results);
        return $results;
    }

    protected function fetchChildren($parentId, &$results)
    {
        $children = $this->db->table($this->tableName)
            ->where($this->parentIdField, $parentId)
            ->get()
            ->getResultArray();

        foreach ($children as $child) {
            $results[] = $child;
            $this->fetchChildren($child[$this->primaryField], $results);
        }
    }

    public function getParents($id): array
    {
        $sql = "WITH RECURSIVE cte_parents AS (
                SELECT 
                t.{$this->primaryField},
                t.{$this->nameField},
                t.{$this->parentIdField}
                FROM {$this->tableName} t
                WHERE t.{$this->primaryField} = :id:

                UNION ALL

                SELECT 
                p.{$this->primaryField},
                p.{$this->nameField},
                p.{$this->parentIdField}
                FROM {$this->tableName} p
                INNER JOIN cte_parents c ON c.{$this->parentIdField} = p.{$this->primaryField}
            )
            SELECT 
                {$this->primaryField} AS id, 
                {$this->nameField} AS name
            FROM cte_parents
            WHERE {$this->primaryField} != :id:";

        $parents = $this->db->query($sql, ['id' => $id])->getResultArray();
        return array_reverse($parents);
    }

    public function getSelect2Data($ids): array
    {
        if (is_array($ids))
            $ids = implode(',', $ids);

        if (is_null($ids) || empty($ids))
            return [];

        $sql = "SELECT 
                    {$this->primaryField} as id,
                    {$this->nameField} as text
                    FROM {$this->tableName} 
                WHERE {$this->primaryField} IN ($ids)";

        $searchResult =  $this->db->query($sql)->getResultArray();

        if (count($searchResult) == 1) {
            return $searchResult[0];
        }

        return $searchResult;
    }
}
