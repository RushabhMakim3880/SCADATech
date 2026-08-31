# 🧱 CI4 Migration Field Rules – Standard Practices

This document defines the required parameters and default conventions for defining fields in all CI4 migration files. Follow strictly for consistency and error-free development.

---

## 🔢 INT / TINYINT / BIGINT

- Always use `UNSIGNED` unless negative values are valid.
- Always define `NULL` or `DEFAULT` explicitly.
- Avoid implicit defaults by MySQL (e.g. defaulting to 0).

```php
'intField' => [
    'type'       => 'INT',
    'constraint' => 11,
    'unsigned'   => true,
    'null'       => false,
    'default'    => 0,
],
```

---

## ✅ BOOLEAN (TINYINT(1))

- Use TINYINT(1) to represent boolean.
- Always set `default` as `0` or `1`.

```php
'isActive' => [
    'type'       => 'TINYINT',
    'constraint' => 1,
    'null'       => false,
    'default'    => 1,
],
```

---

## 💰 DECIMAL

- For financial values: `DECIMAL(18,2)`
- For quantity/percentage: `DECIMAL(10,2)`
- Always `unsigned` if no negative required.
- Always set `default` to `0.00`

```php
'price' => [
    'type'       => 'DECIMAL',
    'constraint' => '18,2',
    'null'       => false,
    'default'    => '0.00',
],
```

---

## 📄 TEXT

- Cannot have default value.
- Always allow `null`.

```php
'description' => [
    'type' => 'TEXT',
    'null' => true,
],
```

---

## 🔡 VARCHAR

- Always define `constraint` (length).
- If optional, use `null => true`, else define `default`.

```php
'email' => [
    'type'       => 'VARCHAR',
    'constraint' => 255,
    'null'       => true,
],
```

---

## 📅 DATETIME / TIMESTAMP

- Allow `null` unless auto-managed.
- If using default, use `'0000-00-00 00:00:00'` (not recommended) or CI4’s `CURRENT_TIMESTAMP` manually via raw SQL if needed.

```php
'createdAt' => [
    'type' => 'DATETIME',
    'null' => true,
],
```

---

## 🔑 Foreign Key Fields

- Always use `INT UNSIGNED NOT NULL DEFAULT 0`
- Define foreign keys in separate migration using `$this->db->disableForeignKeyChecks()` if needed.

```php
'userId' => [
    'type'       => 'INT',
    'constraint' => 11,
    'unsigned'   => true,
    'null'       => false,
    'default'    => 0,
],
```

---

## ⚙️ Default Meta Fields (Mandatory in Every Table)

```php
'createdAt' => ['type' => 'DATETIME', 'null' => true],
'createdBy' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
'updatedAt' => ['type' => 'DATETIME', 'null' => true],
'updatedBy' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
```

---

## ✅ General Rules

- Never rely on implicit MySQL defaults.
- Always define `null` or `default` explicitly.
- Use `unsigned` for all IDs and positive-only numbers.
- For any soft-delete logic, use: `isDeleted` → `TINYINT(1) DEFAULT 0`
