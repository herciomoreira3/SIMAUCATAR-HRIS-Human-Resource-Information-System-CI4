<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Database Connection Debug</h2>";

// Read environment variables using multiple fallback sources
$host = $_ENV['database_default_hostname'] ?? $_SERVER['database_default_hostname'] ?? getenv('database_default_hostname') ??
        $_ENV['database.default.hostname'] ?? $_SERVER['database.default.hostname'] ?? getenv('database.default.hostname') ?? '127.0.0.1';

$port = $_ENV['database_default_port'] ?? $_SERVER['database_default_port'] ?? getenv('database_default_port') ??
        $_ENV['database.default.port'] ?? $_SERVER['database.default.port'] ?? getenv('database.default.port') ?? 3306;

$user = $_ENV['database_default_username'] ?? $_SERVER['database_default_username'] ?? getenv('database_default_username') ??
        $_ENV['database.default.username'] ?? $_SERVER['database.default.username'] ?? getenv('database.default.username') ?? '';

$pass = $_ENV['database_default_password'] ?? $_SERVER['database_default_password'] ?? getenv('database_default_password') ??
        $_ENV['database.default.password'] ?? $_SERVER['database.default.password'] ?? getenv('database.default.password') ?? '';

$db   = $_ENV['database_default_database'] ?? $_SERVER['database_default_database'] ?? getenv('database_default_database') ??
        $_ENV['database.default.database'] ?? $_SERVER['database.default.database'] ?? getenv('database.default.database') ?? '';

$encrypt = $_ENV['database_default_encrypt'] ?? $_SERVER['database_default_encrypt'] ?? getenv('database_default_encrypt') ??
           $_ENV['database.default.encrypt'] ?? $_SERVER['database.default.encrypt'] ?? getenv('database.default.encrypt') ?? '';

echo "Host: " . htmlspecialchars($host) . "<br>";
echo "Port: " . htmlspecialchars($port) . "<br>";
echo "User: " . htmlspecialchars($user) . "<br>";
echo "Database: " . htmlspecialchars($db) . "<br>";
echo "Encryption Config: " . htmlspecialchars($encrypt) . "<br>";

// Let's print all env keys to check if they are set at all
echo "<h3>Available Environment Keys:</h3>";
$allKeys = array_keys(array_merge($_ENV, $_SERVER, getenv()));
sort($allKeys);
foreach ($allKeys as $key) {
    if (strpos(strtolower($key), 'database') !== false || strpos(strtolower($key), 'ci_') !== false || strpos(strtolower($key), 'app') !== false) {
        echo htmlspecialchars($key) . "<br>";
    }
}
echo "<br>";

$mysqli = mysqli_init();
if (!$mysqli) {
    die("mysqli_init failed");
}

// If using ssl_verify or similar for TiDB Cloud
if ($encrypt === 'ssl_verify' || !empty($encrypt)) {
    mysqli_ssl_set($mysqli, NULL, NULL, NULL, NULL, NULL);
}

echo "Connecting...<br>";
if (@mysqli_real_connect($mysqli, $host, $user, $pass, $db, $port, NULL, MYSQLI_CLIENT_SSL)) {
    echo "<span style='color:green;font-weight:bold;'>Success! Connection established.</span><br>";
    $res = mysqli_query($mysqli, "SELECT @@version as version");
    if ($res) {
        $row = mysqli_fetch_assoc($res);
        echo "Server Version: " . htmlspecialchars($row['version']) . "<br>";
    }
    mysqli_close($mysqli);
} else {
    echo "<span style='color:red;font-weight:bold;'>Connection Failed:</span> " . htmlspecialchars(mysqli_connect_error()) . " (Code: " . mysqli_connect_errno() . ")<br>";
}
