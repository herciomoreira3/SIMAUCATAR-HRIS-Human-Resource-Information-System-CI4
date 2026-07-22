<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Database Connection Debug</h2>";

// Read environment variables
$host = getenv('database_default_hostname') ?: getenv('database.default.hostname') ?: '127.0.0.1';
$port = getenv('database_default_port') ?: getenv('database.default.port') ?: 3306;
$user = getenv('database_default_username') ?: getenv('database.default.username') ?: '';
$pass = getenv('database_default_password') ?: getenv('database.default.password') ?: '';
$db   = getenv('database_default_database') ?: getenv('database.default.database') ?: '';
$encrypt = getenv('database_default_encrypt') ?: getenv('database.default.encrypt') ?: '';

echo "Host: " . htmlspecialchars($host) . "<br>";
echo "Port: " . htmlspecialchars($port) . "<br>";
echo "User: " . htmlspecialchars($user) . "<br>";
echo "Database: " . htmlspecialchars($db) . "<br>";
echo "Encryption Config: " . htmlspecialchars($encrypt) . "<br>";

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
