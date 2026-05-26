<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class Settings extends BaseController
{
    public function createRole()
    {
        $createRole = $this->ApplicationModel->createRole($this->request->getPost(null, FILTER_UNSAFE_RAW));
        if ($createRole) {
            session()->setFlashdata('notif_success', '<b>Successfully created role</b> ');
            return redirect()->to(base_url('users'));
        } else {
            session()->setFlashdata('notif_error', '<b>Failed to create role</b> ');
            return redirect()->to(base_url('users'));
        }
    }

    public function updateRole()
    {
        $updateRole = $this->ApplicationModel->updateRole($this->request->getPost(null, FILTER_UNSAFE_RAW));
        if ($updateRole) {
            session()->setFlashdata('notif_success', '<b>Successfully update role</b> ');
            return redirect()->to(base_url('users'));
        } else {
            session()->setFlashdata('notif_error', '<b>Failed to update role</b> ');
            return redirect()->to(base_url('users'));
        }
    }

    public function deleteRole($roleID)
    {
        if (!$roleID) {
            return redirect()->to(base_url('users'));
        }
        $deleteRole = $this->ApplicationModel->deleteRole($roleID);
        if ($deleteRole) {
            session()->setFlashdata('notif_success', '<b>Successfully deleted role</b> ');
            return redirect()->to(base_url('users'));
        } else {
            session()->setFlashdata('notif_error', '<b>Failed to delete role</b> ');
            return redirect()->to(base_url('users'));
        }
    }

    public function createUser()
    {
        if (!$this->validate(['inputUsername' => ['rules' => 'is_unique[users.username]']])) {
            session()->setFlashdata('notif_error', '<b>Failed to add new user</b> The user already exists! ');
            return redirect()->to(base_url('users'));
        }
        $createUser = $this->ApplicationModel->createUser($this->request->getPost(null, FILTER_UNSAFE_RAW));
        if ($createUser) {
            session()->setFlashdata('notif_success', '<b>Successfully added new user</b> ');
            return redirect()->to(base_url('users'));
        } else {
            session()->setFlashdata('notif_error', '<b>Failed to add new user</b> ');
            return redirect()->to(base_url('users'));
        }
    }

    public function users()
    {
        $data = array_merge($this->data, [
            'title'     => 'Users Page',
            'Users'     => $this->ApplicationModel->getUser(),
            'UserRole'  => $this->ApplicationModel->getUserRole()
        ]);
        return view('pages/settings/users', $data);
    }

    public function updateUser()
    {
        $updateUser = $this->ApplicationModel->updateUser($this->request->getPost(null, FILTER_UNSAFE_RAW));
        if ($updateUser) {
            session()->setFlashdata('notif_success', '<b>Successfully update user data</b> ');
            return redirect()->to(base_url('users'));
        } else {
            session()->setFlashdata('notif_error', '<b>Failed to update user data</b> ');
            return redirect()->to(base_url('users'));
        }
    }

    public function deleteUser($userID)
    {
        if (!$userID) {
            return redirect()->to(base_url('users'));
        }
        $deleteUser = $this->ApplicationModel->deleteUser($userID);
        if ($deleteUser) {
            session()->setFlashdata('notif_success', '<b>Successfully delete user</b> ');
            return redirect()->to(base_url('users'));
        } else {
            session()->setFlashdata('notif_error', '<b>Failed to delete user</b> ');
            return redirect()->to(base_url('users'));
        }
    }

    public function roleAccess()
    {
        $role         = $this->request->getGet('role');
        $userRole     = $this->ApplicationModel->getUserRole($role);
        if (!$userRole) {
            return redirect()->to(base_url('users'));
        }
        $data = array_merge($this->data, [
            'title'             => 'Role Access',
            'MenuCategories'    => $this->ApplicationModel->getMenuCategory(),
            'Menus'             => $this->ApplicationModel->getMenu(),
            'Submenus'          => $this->ApplicationModel->getSubmenu(),
            'UserAccess'        => $this->ApplicationModel->getAccessMenu($role),
            'role'              => $this->ApplicationModel->getUserRole($role)
        ]);
        return view('pages/settings/role_access', $data);
    }

    public function changeMenuCategoryPermission()
    {
        $userAccess = $this->ApplicationModel->checkUserMenuCategoryAccess($this->request->getPost(null, FILTER_UNSAFE_RAW));
        if ($userAccess > 0) {
            $this->ApplicationModel->deleteMenuCategoryPermission($this->request->getPost(null, FILTER_UNSAFE_RAW));
        } else {
            $this->ApplicationModel->insertMenuCategoryPermission($this->request->getPost(null, FILTER_UNSAFE_RAW));
        }
    }

    public function changeMenuPermission()
    {
        $userAccess = $this->ApplicationModel->checkUserAccess($this->request->getPost(null, FILTER_UNSAFE_RAW));
        if ($userAccess > 0) {
            $this->ApplicationModel->deleteMenuPermission($this->request->getPost(null, FILTER_UNSAFE_RAW));
        } else {
            $this->ApplicationModel->insertMenuPermission($this->request->getPost(null, FILTER_UNSAFE_RAW));
        }
    }

    public function changeSubMenuPermission()
    {
        $userAccess = $this->ApplicationModel->checkUserSubmenuAccess($this->request->getPost(null, FILTER_UNSAFE_RAW));
        if ($userAccess > 0) {
            $this->ApplicationModel->deleteSubmenuPermission($this->request->getPost(null, FILTER_UNSAFE_RAW));
        } else {
            $this->ApplicationModel->insertSubmenuPermission($this->request->getPost(null, FILTER_UNSAFE_RAW));
        }
    }

    public function menuManagement()
    {
        $data = array_merge($this->data, [
            'title'             => 'Menu Management',
            'MenuCategories'    => $this->ApplicationModel->getMenuCategory(),
            'Menus'             => $this->ApplicationModel->getMenu(),
            'Submenus'          => $this->ApplicationModel->getSubmenu(),
            'validation'        => $this->validation
        ]);
        return view('pages/settings/menu_management', $data);
    }

    public function createMenuCategory()
    {
        if (!$this->validate([
            'inputMenuCategory' => [
                'rules'     => 'required|is_unique[user_menu_category.menu_category]',
                'errors'    => [
                    'required'  => 'Menu Category must be required.',
                    'is_unique' => 'Menu Category cannot be same.'
                ]
            ]
        ])) {
            return redirect()->to('menu-management')->withInput();
        }
        $createMenuCategory = $this->ApplicationModel->createMenuCategory($this->request->getPost(null));
        if ($createMenuCategory) {
            session()->setFlashdata('notif_success', '<b>Successfully create menu category</b>');
            return redirect()->to(base_url('menu-management'));
        } else {
            session()->setFlashdata('notif_error', '<b>Failed to create menu category</b>');
            return redirect()->to(base_url('menu-management'));
        }
    }
    public function updateMenuCategory()
    {
        if (!$this->validate([
            'inputMenuCategory' => [
                'rules'     => 'required|is_unique[user_menu_category.menu_category]',
                'errors'    => [
                    'required'  => 'Menu Category must be required.',
                    'is_unique' => 'Menu Category cannot be same'
                ]
            ]
        ])) {
            return redirect()->to('menu-management')->withInput();
        }
        $updateMenuCategory = $this->ApplicationModel->updateMenuCategory($this->request->getPost(null));
        if ($updateMenuCategory) {
            session()->setFlashdata('notif_success', '<b>Successfully update Menu Category </b> ');
            return redirect()->to(base_url('menu-management'));
        } else {
            session()->setFlashdata('notif_error', '<b>Failed to update Menu Category </b> ');
            return redirect()->to(base_url('menu-management'));
        }
    }

    public function createMenu()
    {
        if (!$this->validate([
            'inputMenuCategory2' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Menu Category must be required.'
                ]
            ],
            'inputMenuTitle' => [
                'rules'     => 'required|is_unique[user_menu.title]',
                'errors'    => [
                    'required'  => 'Menu Title must be required.',
                    'is_unique' => 'Menu Title cannot be same'
                ]
            ],
            'inputMenuURL' => [
                'rules'     => 'required|is_unique[user_menu.url]',
                'errors'    => [
                    'required'  => 'Menu Url must be required.',
                    'is_unique' => 'Menu Url cannot be same'
                ]
            ],
            'inputMenuIcon' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Menu Icon must be required.'
                ]
            ]
        ])) {
            return redirect()->to('menu-management')->withInput();
        }

        $createController   = $this->_createBlankPageController();
        $createView         = $this->_createBlankPageView();

        if ($createController && $createView) {
            $createMenu = $this->ApplicationModel->createMenu($this->request->getPost(null));
            if ($createMenu) {
                $menuTitle          = $this->request->getPost('inputMenuURL');
                $controllerName     = url_title(ucwords($menuTitle), '', false);
                $route              = '$routes->get(\'' . $menuTitle . '\',\'' . $controllerName . '::index\');';
                file_put_contents(APPPATH . 'Config/Routes.php', $route . PHP_EOL, FILE_APPEND | LOCK_EX);
                session()->setFlashdata('notif_success', '<b>Successfully create menu </b> ');
                return redirect()->to(base_url('menu-management'));
            } else {
                session()->setFlashdata('notif_error', '<b>Failed to create menu </b> ');
                return redirect()->to(base_url('menu-management'));
            }
        } else {
            session()->setFlashdata('notif_error', "<b>Failed to create menu </b>Cannot create file ");
            return redirect()->to(base_url('menu-management'));
        }
    }

    public function createSubMenu()
    {
        if (!$this->validate([
            'inputMenu' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Menu must be required.'
                ]
            ],
            'inputSubmenuTitle' => [
                'rules'     => 'required|is_unique[user_submenu.title]',
                'errors'    => [
                    'required'  => 'Submenu Title must be required.',
                    'is_unique' => 'Submenu Title cannot be same'
                ]
            ],
            'inputSubmenuURL' => [
                'rules'     => 'required|is_unique[user_submenu.url]',
                'errors'    => [
                    'required'  => 'Submenu Url must be required.',
                    'is_unique' => 'Submenu Url cannot be same'
                ]
            ],
        ])) {
            session()->setFlashdata('notif_error', $this->validation->getErrors());
            return redirect()->to('menu-management')->withInput();
        }
        $createSubMenu = $this->ApplicationModel->createSubMenu($this->request->getPost(null));
        if ($createSubMenu) {
            session()->setFlashdata('notif_success', '<b>Successfully create submenu </b> ');
            return redirect()->to(base_url('menu-management'));
        } else {
            session()->setFlashdata('notif_error', '<b>Failed to create submenu </b> ');
            return redirect()->to(base_url('menu-management'));
        }
    }

    private function _createBlankPageController()
    {
        $menuTitle          = ucwords($this->request->getPost('inputMenuURL'));
        $controllerName     = url_title(ucwords($menuTitle), '', false);
        $viewName           = url_title($menuTitle, '', true);
        $controllerPath     = APPPATH . 'Controllers/' . $controllerName . ".php";
        $controllerContent  = "<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class $controllerName extends BaseController
{
    public function index()
    {
        $|data = array_merge($|this->data, [
            'title'         => '$menuTitle'
        ]);
        return view('$viewName', $|data);
    }
}
		";
        $renderFile = str_replace("|", "", $controllerContent);
        if (file_put_contents($controllerPath, $renderFile) !== false) {
            return true;
        } else {
            return false;
        }
    }

    private function _createBlankPageView()
    {
        $viewName        = url_title($this->request->getPost('inputMenuURL'), '', true);
        $viewPath        = APPPATH . 'Views/' . $viewName . ".php";
        $viewContent     = "<?= $|this->extend('layouts/main'); ?>
<?= $|this->section('content'); ?>
<h1 class=\"h3 mb-3\"><strong><?= $|title; ?></strong> Menu </h1>
<?= $|this->endSection(); ?>
		";
        $renderFile = str_replace("|", "", $viewContent);
        if (file_put_contents($viewPath, $renderFile) !== false) {
            return true;
        } else {
            return false;
        }
    }
}
