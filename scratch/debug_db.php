<?php
$host = '127.0.0.1';
$user = 'root';
$pass = '';
$db   = 'starterpanel';

$conn = new mysqli($host, $user, $pass, $db);

echo "--- user_role ---\n";
$res = $conn->query("SELECT * FROM user_role");
while($row = $res->fetch_assoc()) print_r($row);

echo "\n--- user_menu ---\n";
$res = $conn->query("SELECT * FROM user_menu");
while($row = $res->fetch_assoc()) print_r($row);

echo "\n--- user_access ---\n";
$res = $conn->query("SELECT * FROM user_access WHERE role_id = 2");
while($row = $res->fetch_assoc()) print_r($row);

echo "\n--- users ---\n";
$res = $conn->query("SELECT * FROM users WHERE username = 'admin@gmail.com'");
while($row = $res->fetch_assoc()) print_r($row);

$conn->close();
?>
