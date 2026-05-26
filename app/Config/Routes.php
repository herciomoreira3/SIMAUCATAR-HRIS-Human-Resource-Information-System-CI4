<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Auth::index');
$routes->get('login', 'Auth::index');
$routes->post('login', 'Auth::index');
$routes->get('logout', 'Auth::logout');
$routes->get('blocked', 'Auth::forbiddenPage');
$routes->get('register', 'Auth::register');
$routes->post('register', 'Auth::registration');

$routes->get('dashboard', 'Home::index');

// Setting Routes
$routes->group('users', static function ($routes) {
    $routes->get('/', 'Settings::users');
    $routes->post('create-role', 'Settings::createRole');
    $routes->post('update-role', 'Settings::updateRole');
    $routes->delete('delete-role/(:num)', 'Settings::deleteRole/$1');

    $routes->get('role-access', 'Settings::roleAccess');
    $routes->post('create-user', 'Settings::createUser');
    $routes->post('update-user', 'Settings::updateUser');
    $routes->delete('delete-user/(:num)', 'Settings::deleteUser/$1');

    $routes->post('change-menu-permission', 'Settings::changeMenuPermission');
    $routes->post('change-menu-category-permission', 'Settings::changeMenuCategoryPermission');
    $routes->post('change-submenu-permission', 'Settings::changeSubMenuPermission');
});

$routes->group('menu-management', static function ($routes) {
    $routes->get('/', 'Settings::menuManagement');
    $routes->post('create-menu-category', 'Settings::createMenuCategory');
    $routes->post('create-menu', 'Settings::createMenu');
    $routes->post('create-submenu', 'Settings::createSubMenu');
});
$routes->get('menu','Menu::index');

// --- HRIS ROUTES ---

// Administrador
$routes->group('administrador', static function ($routes) {
    $routes->get('dashboard', 'Administrador::dashboard');
    
    // Master Data
    $routes->get('departamentu', 'Administrador::departamentu');
    $routes->post('departamentu', 'Administrador::createDepartamentu');
    $routes->post('departamentu/update/(:num)', 'Administrador::updateDepartamentu/$1');
    $routes->get('departamentu/delete/(:num)', 'Administrador::deleteDepartamentu/$1');
    $routes->get('pozisaun', 'Administrador::pozisaun');
    $routes->post('pozisaun', 'Administrador::createPozisaun');
    $routes->post('pozisaun/update/(:num)', 'Administrador::updatePozisaun/$1');
    $routes->get('pozisaun/delete/(:num)', 'Administrador::deletePozisaun/$1');
    $routes->get('kategoria', 'Administrador::kategoria');
    $routes->post('kategoria', 'Administrador::createKategoria');
    $routes->post('kategoria/update/(:num)', 'Administrador::updateKategoria/$1');
    $routes->get('kategoria/delete/(:num)', 'Administrador::deleteKategoria/$1');
    
    // HR Management
    $routes->get('funsionariu', 'Administrador::funsionariu');
    $routes->post('funsionariu', 'Administrador::saveFunsionariu');
    $routes->post('funsionariu/update/(:num)', 'Administrador::updateFunsionariu/$1');
    $routes->get('funsionariu/delete/(:num)', 'Administrador::deleteFunsionariu/$1');
    $routes->get('prezensa', 'Administrador::prezensa');
    $routes->post('prezensa/settings', 'Administrador::updateAttendanceSettings');
    $routes->get('lisensa', 'Administrador::lisensa');
    $routes->post('lisensa/aprova/(:num)', 'Administrador::aprovaLisensa/$1');
    $routes->get('salariu', 'Administrador::salariu');
    $routes->get('salariu/status', 'Administrador::getPaymentStatus');
    $routes->post('salariu/prosesa', 'Administrador::prosesaSalariu');
    $routes->post('subsidiu', 'Administrador::createSubsidiu');
    $routes->post('subsidiu/update/(:num)', 'Administrador::updateSubsidiu/$1');
    $routes->get('subsidiu/delete/(:num)', 'Administrador::deleteSubsidiu/$1');
    $routes->get('avizu', 'Administrador::avizu');
    $routes->post('avizu', 'Administrador::createAvizu');
    $routes->get('avizu/delete/(:num)', 'Administrador::deleteAvizu/$1');
    $routes->post('avizu/expiration/(:num)', 'Administrador::setExpiration/$1');
    $routes->get('sansaun', 'Administrador::sansaun');
    $routes->post('sansaun', 'Administrador::createSansaun');
    $routes->get('sansaun/detail/(:num)', 'Administrador::getSansaunDetail/$1');
    $routes->get('sansaun/retira/(:num)', 'Administrador::retiraSansaun/$1');
    $routes->post('sansaun/jera_absensia', 'Administrador::jeraSansaunAbsensia');
    $routes->post('tipu_sansaun', 'Administrador::createTipuSansaun');
    $routes->get('tipu_sansaun/delete/(:num)', 'Administrador::deleteTipuSansaun/$1');

    // Módulu Relatóriu
    $routes->group('relatoriu', static function ($routes) {
        $routes->get('/', 'Relatoriu::index');
        $routes->get('funsionariu', 'Relatoriu::funsionariu');
        $routes->get('prezensa', 'Relatoriu::prezensa');
        $routes->get('salariu', 'Relatoriu::salariu');
        $routes->get('lisensa', 'Relatoriu::lisensa');
        $routes->get('sansaun', 'Relatoriu::sansaun');
        
        $routes->post('export/funsionariu', 'Relatoriu::exportFunsionariu');
        $routes->post('export/prezensa', 'Relatoriu::exportPrezensa');
        $routes->post('export/salariu', 'Relatoriu::exportSalariu');
        $routes->post('export/lisensa', 'Relatoriu::exportLisensa');
        $routes->post('export/sansaun', 'Relatoriu::exportSansaun');
    });
});

// Funsionariu
$routes->group('funsionariu', static function ($routes) {
    $routes->get('dashboard', 'Funsionariu::dashboard');
    $routes->get('prezensa', 'Funsionariu::prezensa');
    $routes->post('prezensa/tama', 'Funsionariu::clockIn');
    $routes->post('prezensa/sai', 'Funsionariu::clockOut');
    $routes->get('perfil', 'Funsionariu::perfil');
    $routes->get('lisensa', 'Funsionariu::lisensa');
    $routes->post('lisensa', 'Funsionariu::saveLisensa');
    $routes->get('salariu', 'Funsionariu::salariu');
});
