<?php
/**
 * NiRu-Furnitures — Database Wrapper
 * -------------------------------------------------------
 * Creates a global $conn (MySQLi) and provides helper
 * functions for safe prepared-statement queries.
 *
 * Included automatically via config.php.
 */

// ── Create connection ─────────────────────────────────────────
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    if (ENVIRONMENT === 'development') {
        die('<div style="font-family:monospace;color:red;padding:2rem">
             <strong>DB Connection Failed:</strong> ' . htmlspecialchars($conn->connect_error) . '</div>');
    } else {
        die('A database error occurred. Please try again later.');
    }
}

$conn->set_charset(DB_CHARSET);

// ── Helper: execute a prepared statement ─────────────────────
/**
 * dbQuery(string $sql, string $types, mixed ...$params): mysqli_stmt|false
 *
 * Example:
 *   $stmt = dbQuery("SELECT * FROM products WHERE id = ? AND status = ?", "is", $id, 'active');
 *   $result = $stmt->get_result();
 */
function dbQuery(string $sql, string $types = '', ...$params): mysqli_stmt|false
{
    global $conn;
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        if (ENVIRONMENT === 'development') {
            trigger_error('SQL Prepare Error: ' . $conn->error . ' | SQL: ' . $sql, E_USER_ERROR);
        }
        return false;
    }
    if ($types && $params) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    return $stmt;
}

/**
 * dbFetchAll(string $sql, string $types = '', mixed ...$params): array
 * Returns all rows as an associative array.
 */
function dbFetchAll(string $sql, string $types = '', ...$params): array
{
    $stmt = dbQuery($sql, $types, ...$params);
    if (!$stmt) return [];
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

/**
 * dbFetchOne(string $sql, string $types = '', mixed ...$params): array|null
 * Returns the first row or null.
 */
function dbFetchOne(string $sql, string $types = '', ...$params): ?array
{
    $stmt = dbQuery($sql, $types, ...$params);
    if (!$stmt) return null;
    $row = $stmt->get_result()->fetch_assoc();
    return $row ?: null;
}

/**
 * dbInsert(string $sql, string $types, mixed ...$params): int
 * Returns the last inserted ID (0 on failure).
 */
function dbInsert(string $sql, string $types = '', ...$params): int
{
    global $conn;
    $stmt = dbQuery($sql, $types, ...$params);
    if (!$stmt) return 0;
    return (int) $conn->insert_id;
}

/**
 * dbExecute(string $sql, string $types, mixed ...$params): bool
 * Executes UPDATE / DELETE. Returns true on success.
 */
function dbExecute(string $sql, string $types = '', ...$params): bool
{
    $stmt = dbQuery($sql, $types, ...$params);
    return $stmt !== false;
}

/**
 * getSetting(string $key, string $default = ''): string
 * Reads a single value from the settings table.
 */
function getSetting(string $key, string $default = ''): string
{
    $row = dbFetchOne("SELECT setting_value FROM settings WHERE setting_key = ?", 's', $key);
    return $row ? (string) $row['setting_value'] : $default;
}
