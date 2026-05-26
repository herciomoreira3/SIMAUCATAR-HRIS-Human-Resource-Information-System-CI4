<?php
$host = '127.0.0.1';
$user = 'root';
$pass = '';
$db   = 'starterpanel';

$conn = new mysqli($host, $user, $pass, $db);

// Role 2: administrador
// Role 3: funsionariu

// Menu Categories: 3, 4, 5, 6
$categories = [3, 4, 5, 6];
foreach ($categories as $catId) {
    $conn->query("INSERT INTO user_access (role_id, menu_category_id, menu_id, submenu_id) VALUES (2, $catId, 0, 0)");
}

// Admin Menus: 4 to 13
for ($i = 4; $i <= 13; $i++) {
    $conn->query("INSERT INTO user_access (role_id, menu_id, menu_category_id, submenu_id) VALUES (2, $i, 0, 0)");
}

// Funsionariu Access (Role 3)
$f_categories = [3, 6];
foreach ($f_categories as $catId) {
    $conn->query("INSERT INTO user_access (role_id, menu_category_id, menu_id, submenu_id) VALUES (3, $catId, 0, 0)");
}
// Funsionariu Menus: 14 to 18
for ($i = 14; $i <= 18; $i++) {
    $conn->query("INSERT INTO user_access (role_id, menu_id, menu_category_id, submenu_id) VALUES (3, $i, 0, 0)");
}

echo "Permissions added successfully.\n";
$conn->close();
?>
