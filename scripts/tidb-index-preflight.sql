-- Read-only prerequisite for any future TiDB index migration. Do not execute
-- candidate DDL until these results and one before/after EXPLAIN are reviewed.
SHOW CREATE TABLE prezensa;
SHOW CREATE TABLE lisensa;
SHOW CREATE TABLE user_access;
SHOW CREATE TABLE user_menu;
SHOW CREATE TABLE user_submenu;
SHOW CREATE TABLE avizu;
SHOW CREATE TABLE sansaun;
SHOW CREATE TABLE salariu;

SHOW INDEX FROM prezensa;
SHOW INDEX FROM lisensa;
SHOW INDEX FROM user_access;
SHOW INDEX FROM user_menu;
SHOW INDEX FROM user_submenu;
SHOW INDEX FROM avizu;
SHOW INDEX FROM sansaun;
SHOW INDEX FROM salariu;
