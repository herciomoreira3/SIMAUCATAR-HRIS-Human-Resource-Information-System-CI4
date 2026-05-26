<?php
$conn = new mysqli('127.0.0.1', 'root', '', 'starterpanel');
$conn->query("INSERT IGNORE INTO user_access (role_id, menu_id, menu_category_id, submenu_id) VALUES (2, 1, 0, 0)");
$conn->query("INSERT IGNORE INTO user_access (role_id, menu_id, menu_category_id, submenu_id) VALUES (3, 1, 0, 0)");
$conn->close();
echo "Done";
?>
