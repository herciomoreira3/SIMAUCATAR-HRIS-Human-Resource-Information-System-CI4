<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Repositories\NavigationRepository;
use App\Services\NavigationService;
use CodeIgniter\HTTP\ResponseInterface;

class Settings extends BaseController
{
    private function invalidateNavigation(): void
    {
        (new NavigationService(new NavigationRepository($this->db), cache()))->invalidate();
    }
    public function createRole()
    {
        $createRole = $this->ApplicationModel->createRole($this->request->getPost(null, FILTER_UNSAFE_RAW));
        if ($createRole) {
            session()->setFlashdata('notif_success', '<b>Papel kria ona.</b> ');
            return redirect()->to(base_url('users'));
        } else {
            session()->setFlashdata('notif_error', '<b>La konsege kria papel.</b> ');
            return redirect()->to(base_url('users'));
        }
    }

    public function updateRole()
    {
        $updateRole = $this->ApplicationModel->updateRole($this->request->getPost(null, FILTER_UNSAFE_RAW));
        if ($updateRole) {
            session()->setFlashdata('notif_success', '<b>Papel atualiza ona.</b> ');
            return redirect()->to(base_url('users'));
        } else {
            session()->setFlashdata('notif_error', '<b>La konsege atualiza papel.</b> ');
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
            $this->invalidateNavigation();
            session()->setFlashdata('notif_success', '<b>Papel hamos ona.</b> ');
            return redirect()->to(base_url('users'));
        } else {
            session()->setFlashdata('notif_error', '<b>La konsege hamos papel.</b> ');
            return redirect()->to(base_url('users'));
        }
    }

    public function createUser()
    {
        if (!$this->validate(['inputUsername' => ['rules' => 'is_unique[users.username]']])) {
            session()->setFlashdata('notif_error', '<b>La konsege aumenta utilizador foun.</b> Utilizador ne\'e iha ona! ');
            return redirect()->to(base_url('users'));
        }
        $createUser = $this->ApplicationModel->createUser($this->request->getPost(null, FILTER_UNSAFE_RAW));
        if ($createUser) {
            session()->setFlashdata('notif_success', '<b>Utilizador foun aumenta ona.</b> ');
            return redirect()->to(base_url('users'));
        } else {
            session()->setFlashdata('notif_error', '<b>La konsege aumenta utilizador foun.</b> ');
            return redirect()->to(base_url('users'));
        }
    }

    public function users()
    {
        $data = array_merge($this->data, [
            'title'     => 'Pajina Utilizador',
            'Users'     => $this->ApplicationModel->getUser(),
            'UserRole'  => $this->ApplicationModel->getUserRole()
        ]);
        return view('pages/settings/users', $data);
    }

    public function updateUser()
    {
        $updateUser = $this->ApplicationModel->updateUser($this->request->getPost(null, FILTER_UNSAFE_RAW));
        if ($updateUser) {
            session()->setFlashdata('notif_success', '<b>Dadus utilizador atualiza ona.</b> ');
            return redirect()->to(base_url('users'));
        } else {
            session()->setFlashdata('notif_error', '<b>La konsege atualiza dadus utilizador.</b> ');
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
            session()->setFlashdata('notif_success', '<b>Utilizador hamos ona.</b> ');
            return redirect()->to(base_url('users'));
        } else {
            session()->setFlashdata('notif_error', '<b>La konsege hamos utilizador.</b> ');
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
            'title'             => 'Asesu Papel',
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
        $this->invalidateNavigation();
    }

    public function changeMenuPermission()
    {
        $userAccess = $this->ApplicationModel->checkUserAccess($this->request->getPost(null, FILTER_UNSAFE_RAW));
        if ($userAccess > 0) {
            $this->ApplicationModel->deleteMenuPermission($this->request->getPost(null, FILTER_UNSAFE_RAW));
        } else {
            $this->ApplicationModel->insertMenuPermission($this->request->getPost(null, FILTER_UNSAFE_RAW));
        }
        $this->invalidateNavigation();
    }

    public function changeSubMenuPermission()
    {
        $userAccess = $this->ApplicationModel->checkUserSubmenuAccess($this->request->getPost(null, FILTER_UNSAFE_RAW));
        if ($userAccess > 0) {
            $this->ApplicationModel->deleteSubmenuPermission($this->request->getPost(null, FILTER_UNSAFE_RAW));
        } else {
            $this->ApplicationModel->insertSubmenuPermission($this->request->getPost(null, FILTER_UNSAFE_RAW));
        }
        $this->invalidateNavigation();
    }

    public function menuManagement()
    {
        $data = array_merge($this->data, [
            'title'             => 'Jestaun Menu',
            'MenuCategories'    => $this->ApplicationModel->getMenuCategory(),
            'Menus'             => $this->ApplicationModel->getMenu(),
            'Submenus'          => $this->ApplicationModel->getSubmenu(),
            'validation'        => service('validation')
        ]);
        return view('pages/settings/menu_management', $data);
    }

    public function createMenuCategory()
    {
        if (!$this->validate([
            'inputMenuCategory' => [
                'rules'     => 'required|is_unique[user_menu_category.menu_category]',
                'errors'    => [
                    'required'  => 'Kategoria menu obrigatoriu.',
                    'is_unique' => 'Kategoria menu labele hanesan.'
                ]
            ]
        ])) {
            return redirect()->to('menu-management')->withInput();
        }
        $createMenuCategory = $this->ApplicationModel->createMenuCategory($this->request->getPost(null));
        if ($createMenuCategory) {
            $this->invalidateNavigation();
            session()->setFlashdata('notif_success', '<b>Kategoria menu kria ona.</b>');
            return redirect()->to(base_url('menu-management'));
        } else {
            session()->setFlashdata('notif_error', '<b>La konsege kria kategoria menu.</b>');
            return redirect()->to(base_url('menu-management'));
        }
    }
    public function updateMenuCategory()
    {
        if (!$this->validate([
            'inputMenuCategory' => [
                'rules'     => 'required|is_unique[user_menu_category.menu_category]',
                'errors'    => [
                    'required'  => 'Kategoria menu obrigatoriu.',
                    'is_unique' => 'Kategoria menu labele hanesan.'
                ]
            ]
        ])) {
            return redirect()->to('menu-management')->withInput();
        }
        $updateMenuCategory = $this->ApplicationModel->updateMenuCategory($this->request->getPost(null));
        if ($updateMenuCategory) {
            $this->invalidateNavigation();
            session()->setFlashdata('notif_success', '<b>Kategoria menu atualiza ona.</b> ');
            return redirect()->to(base_url('menu-management'));
        } else {
            session()->setFlashdata('notif_error', '<b>La konsege atualiza kategoria menu.</b> ');
            return redirect()->to(base_url('menu-management'));
        }
    }

    public function createMenu()
    {
        if (!$this->validate([
            'inputMenuCategory2' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Kategoria menu obrigatoriu.'
                ]
            ],
            'inputMenuTitle' => [
                'rules'     => 'required|is_unique[user_menu.title]',
                'errors'    => [
                    'required'  => 'Titulu menu obrigatoriu.',
                    'is_unique' => 'Titulu menu labele hanesan.'
                ]
            ],
            'inputMenuURL' => [
                'rules'     => 'required|is_unique[user_menu.url]',
                'errors'    => [
                    'required'  => 'URL menu obrigatoriu.',
                    'is_unique' => 'URL menu labele hanesan.'
                ]
            ],
            'inputMenuIcon' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Ikone menu obrigatoriu.'
                ]
            ]
        ])) {
            return redirect()->to('menu-management')->withInput();
        }

        $createMenu = $this->ApplicationModel->createMenu($this->request->getPost(null));
        if ($createMenu) {
            $this->invalidateNavigation();
            session()->setFlashdata('notif_success', '<b>Metadata menu kria ona.</b> Aumenta route/controller liu husi code no migration.');
            return redirect()->to(base_url('menu-management'));
        }

        session()->setFlashdata('notif_error', '<b>La konsege kria menu.</b> ');
        return redirect()->to(base_url('menu-management'));
    }

    public function createSubMenu()
    {
        if (!$this->validate([
            'inputMenu' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Menu obrigatoriu.'
                ]
            ],
            'inputSubmenuTitle' => [
                'rules'     => 'required|is_unique[user_submenu.title]',
                'errors'    => [
                    'required'  => 'Titulu submenu obrigatoriu.',
                    'is_unique' => 'Titulu submenu labele hanesan.'
                ]
            ],
            'inputSubmenuURL' => [
                'rules'     => 'required|is_unique[user_submenu.url]',
                'errors'    => [
                    'required'  => 'URL submenu obrigatoriu.',
                    'is_unique' => 'URL submenu labele hanesan.'
                ]
            ],
        ])) {
            session()->setFlashdata('notif_error', service('validation')->getErrors());
            return redirect()->to('menu-management')->withInput();
        }
        $createSubMenu = $this->ApplicationModel->createSubMenu($this->request->getPost(null));
        if ($createSubMenu) {
            $this->invalidateNavigation();
            session()->setFlashdata('notif_success', '<b>Submenu kria ona.</b> ');
            return redirect()->to(base_url('menu-management'));
        } else {
            session()->setFlashdata('notif_error', '<b>La konsege kria submenu.</b> ');
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
