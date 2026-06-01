<?php

namespace App\Models;

use CodeIgniter\Model;

class ApplicationModel extends Model
{
    public function getMenuCategory($menuCategoryID = false)
    {
        if ($menuCategoryID) {
            return $this->db->table('user_menu_category')->where(['id' => $menuCategoryID['id']])->get()->getRowArray();
        }
        return $this->db->table('user_menu_category')->get()->getResultArray();
    }
    public function getMenu($menuID = false)
    {
        if ($menuID) {
            return $this->db->table('user_menu')
                ->select('*,user_menu_category.menu_category AS category,user_menu.menu_category AS menu_category_id,user_menu.id AS menu_id')
                ->join('user_menu_category', 'user_menu.menu_category = user_menu_category.id')
                ->where(['id' => $menuID['menu_id']])
                ->get()->getRowArray();
        }
        return $this->db->table('user_menu')
            ->select('*,user_menu_category.menu_category AS category,user_menu.menu_category AS menu_category_id,user_menu.id AS menu_id')
            ->join('user_menu_category', 'user_menu.menu_category = user_menu_category.id')
            ->get()->getResultArray();
    }

    public function getSubmenu()
    {
        return $this->db->table('user_submenu')->select('*, user_menu.title AS menu_title, user_submenu.menu AS menu_id, user_submenu.id AS submenu_id, user_submenu.title AS submenu_title, user_submenu.url AS submenu_url')
            ->join('user_menu', 'user_submenu.menu = user_menu.id')
            ->join('user_menu_category', 'user_menu.menu_category = user_menu_category.id')
            ->get()->getResultArray();
    }

    public function createMenuCategory($dataMenuCategory)
    {
        $this->db->transBegin();
        $this->db->table('user_menu_category')->insert(['menu_category' => $dataMenuCategory['inputMenuCategory']]);
        $menuCategoryID = $this->db->insertID();
        $this->db->table('user_access')->insert(['role_id' => 1, 'menu_category_id' => $menuCategoryID]);
        if ($this->db->transStatus() === FALSE) {
            $this->db->transRollback();
            return false;
        } else {
            $this->db->transCommit();
            return true;
        }
    }

    public function updateMenuCategory($menuCategoryID)
    {
        return $this->db->table('user_menu_category')
            ->where('id', $menuCategoryID['menuCategoryID'] ?? $menuCategoryID['id'] ?? 0)
            ->update(['menu_category' => $menuCategoryID['inputMenuCategory']]);
    }

    public function createMenu($dataMenu)
    {
        $this->db->transBegin();
        $this->db->table('user_menu')->insert([
            'menu_category' => $dataMenu['inputMenuCategory2'],
            'title'         => $dataMenu['inputMenuTitle'],
            'url'           => $dataMenu['inputMenuURL'],
            'icon'          => $dataMenu['inputMenuIcon'],
            'parent'        => 0
        ]);
        $menuID = $this->db->insertID();
        $this->db->table('user_access')->insert(['role_id' => 1, 'menu_id' => $menuID]);
        if ($this->db->transStatus() === FALSE) {
            $this->db->transRollback();
            return false;
        } else {
            $this->db->transCommit();
            return true;
        }
    }

    public function createSubMenu($dataSubmenu)
    {
        $this->db->transBegin();
        $this->db->table('user_submenu')->insert([
            'menu'            => $dataSubmenu['inputMenu'],
            'title'           => $dataSubmenu['inputSubmenuTitle'],
            'url'             => $dataSubmenu['inputSubmenuURL']
        ]);
        $submenuID = $this->db->insertID();
        $this->db->table('user_access')->insert(['role_id' => 1, 'submenu_id' => $submenuID]);
        $this->db->table('user_menu')->update(['parent' => 1], ['id' => $dataSubmenu['inputMenu']]);
        if ($this->db->transStatus() === FALSE) {
            $this->db->transRollback();
            return false;
        } else {
            $this->db->transCommit();
            return true;
        }
    }

    public function getMenuByUrl($menuUrl)
    {
        return $this->db->table('user_menu')->where(['url' => $menuUrl])->get()->getRowArray();
    }

    public function getUser($username = false, $userID = false)
    {
        // Try old users table first, then utilizador
        if ($username) {
            $userFields = $this->db->getFieldNames('users');
            $select = 'users.*, users.id AS userID, user_role.id AS role_id, user_role.role_name AS role';
            if (in_array('status', $userFields, true)) {
                $select .= ', users.status AS status';
            }

            $user = $this->db->table('users')
                ->select($select)
                ->join('user_role', 'users.role = user_role.id')
                ->where('users.username', $username);

            if (in_array('email', $userFields, true)) {
                $user->orWhere('users.email', $username);
            }

            $user = $user->get()->getRowArray();
            if ($user) {
                $user['_auth_table'] = 'users';
            }
            
            if (!$user) {
                $user = $this->db->table('utilizador')
                    ->select('*, utilizador.id AS userID, papel.id AS role_id, naran_utilizador AS username, xave_secreta AS password, naran_papel AS role, estadu_kontu AS status')
                    ->join('papel', 'utilizador.papel_id = papel.id')
                    ->where(['naran_utilizador' => $username])
                    ->get()->getRowArray();
                
                if ($user) {
                    $user['_auth_table'] = 'utilizador';
                    $funsionariu = $this->db->table('funsionariu')->where('utilizador_id', $user['userID'])->get()->getRowArray();
                    $user['fullname'] = $funsionariu ? $funsionariu['naran_kompletu'] : $user['username'];
                    $user['foto_perfil'] = $funsionariu ? $funsionariu['foto_perfil'] : null;
                }
            }
            return $user;
        } elseif ($userID) {
            return $this->db->table('users')
                ->select('users.*, users.id AS userID, user_role.id AS role_id, user_role.role_name AS role')
                ->join('user_role', 'users.role = user_role.id')
                ->where(['users.id' => $userID])
                ->get()->getRowArray();
        } else {
            return $this->db->table('users')
                ->select('users.*, users.id AS userID, user_role.id AS role_id, user_role.role_name AS role')
                ->join('user_role', 'users.role = user_role.id')
                ->get()->getResultArray();
        }
    }

    // --- HRIS NEW METHODS ---

    public function getUtilizador($id = false) {
        if ($id) return $this->db->table('utilizador')->select('utilizador.*, papel.naran_papel')->join('papel', 'utilizador.papel_id = papel.id')->where('utilizador.id', $id)->get()->getRowArray();
        return $this->db->table('utilizador')->select('utilizador.*, papel.naran_papel')->join('papel', 'utilizador.papel_id = papel.id')->get()->getResultArray();
    }

    public function getPapel($id = false) {
        if ($id) return $this->db->table('papel')->where('id', $id)->get()->getRowArray();
        return $this->db->table('papel')->get()->getResultArray();
    }

    public function getDepartamentu($id = false) {
        if ($id) return $this->db->table('departamentu')->where('id', $id)->get()->getRowArray();
        return $this->db->table('departamentu')->get()->getResultArray();
    }

    public function getPozisaun($id = false) {
        if ($id) return $this->db->table('pozisaun')->where('id', $id)->get()->getRowArray();
        return $this->db->table('pozisaun')->get()->getResultArray();
    }

    public function getKategoria($id = false) {
        if ($id) return $this->db->table('kategoria')->where('id', $id)->get()->getRowArray();
        return $this->db->table('kategoria')->get()->getResultArray();
    }

    public function getFunsionariu($id = false) {
        $builder = $this->db->table('funsionariu')
            ->select('funsionariu.*, departamentu.naran_departamentu, pozisaun.naran_pozisaun, pozisaun.salariu_baziku, kategoria.naran_kategoria, users.username AS naran_utilizador, users.role AS role_id, "Ativu" AS estadu_kontu')
            ->join('departamentu', 'funsionariu.departamentu_id = departamentu.id')
            ->join('pozisaun', 'funsionariu.pozisaun_id = pozisaun.id')
            ->join('kategoria', 'funsionariu.kategoria_id = kategoria.id')
            ->join('users', 'funsionariu.utilizador_id = users.id', 'left');
        
        if ($id) return $builder->where('funsionariu.id', $id)->get()->getRowArray();
        return $builder->get()->getResultArray();
    }

    public function getFunsionariuByUserId($userId) {
        return $this->db->table('funsionariu')
            ->select('funsionariu.*, departamentu.naran_departamentu, pozisaun.naran_pozisaun, kategoria.naran_kategoria, users.username AS naran_utilizador, users.role AS role_id')
            ->join('departamentu', 'funsionariu.departamentu_id = departamentu.id', 'left')
            ->join('pozisaun', 'funsionariu.pozisaun_id = pozisaun.id', 'left')
            ->join('kategoria', 'funsionariu.kategoria_id = kategoria.id', 'left')
            ->join('users', 'funsionariu.utilizador_id = users.id', 'left')
            ->where('funsionariu.utilizador_id', $userId)
            ->get()->getRowArray();
    }

    public function getPrezensa($id = false, $funsionariu_id = false, $data = false) {
        $builder = $this->db->table('prezensa')
            ->select('prezensa.*, funsionariu.naran_kompletu, funsionariu.nid')
            ->join('funsionariu', 'prezensa.funsionariu_id = funsionariu.id', 'left');
        
        if ($id) {
            return $builder->where('prezensa.id', $id)->get()->getRowArray();
        }
        
        if ($funsionariu_id) {
            $builder->where('prezensa.funsionariu_id', $funsionariu_id);
        }
        
        if ($data) {
            $builder->where('prezensa.data_prezensa', $data);
        }
        
        return $builder->orderBy('prezensa.data_prezensa', 'DESC')->get()->getResultArray();
    }

    public function getLisensa($id = false, $funsionariu_id = false, $estadu = false) {
        $builder = $this->db->table('lisensa')
            ->select('lisensa.*, funsionariu.naran_kompletu, funsionariu.nid')
            ->join('funsionariu', 'lisensa.funsionariu_id = funsionariu.id');
        
        if ($id) return $builder->where('lisensa.id', $id)->get()->getRowArray();
        if ($funsionariu_id) $builder->where('lisensa.funsionariu_id', $funsionariu_id);
        if ($estadu) $builder->where('lisensa.estadu_lisensa', $estadu);
        
        return $builder->get()->getResultArray();
    }

    public function countLeaveDays(string $startDate, string $endDate, ?int $year = null): int
    {
        $start = new \DateTime($startDate);
        $end = new \DateTime($endDate);

        if ($year !== null) {
            $yearStart = new \DateTime($year . '-01-01');
            $yearEnd = new \DateTime($year . '-12-31');
            if ($end < $yearStart || $start > $yearEnd) {
                return 0;
            }
            if ($start < $yearStart) {
                $start = $yearStart;
            }
            if ($end > $yearEnd) {
                $end = $yearEnd;
            }
        }

        return ((int) $start->diff($end)->format('%a')) + 1;
    }

    public function getLeaveBalances($funsionariu_id = false, $year = false): array
    {
        $builder = $this->db->table('leave_balances')
            ->select('leave_balances.*, funsionariu.nid, funsionariu.naran_kompletu')
            ->join('funsionariu', 'leave_balances.funsionariu_id = funsionariu.id', 'left');

        if ($funsionariu_id) {
            $builder->where('leave_balances.funsionariu_id', (int) $funsionariu_id);
        }
        if ($year) {
            $builder->where('leave_balances.year', (int) $year);
        }

        return $builder->orderBy('leave_balances.year', 'DESC')
            ->orderBy('funsionariu.naran_kompletu', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function ensureLeaveBalance(int $funsionariuId, string $leaveType, int $year, float $entitlement = 12.0): array
    {
        $balance = $this->db->table('leave_balances')
            ->where('funsionariu_id', $funsionariuId)
            ->where('leave_type', $leaveType)
            ->where('year', $year)
            ->get()->getRowArray();

        if ($balance) {
            return $balance;
        }

        $now = date('Y-m-d H:i:s');
        $data = [
            'funsionariu_id' => $funsionariuId,
            'leave_type' => $leaveType,
            'year' => $year,
            'entitlement_days' => $entitlement,
            'used_days' => 0,
            'pending_days' => 0,
            'remaining_days' => $entitlement,
            'created_at' => $now,
            'updated_at' => $now,
        ];
        $this->db->table('leave_balances')->insert($data);
        $data['id'] = $this->db->insertID();

        return $data;
    }

    public function recalculateLeaveBalance(int $funsionariuId, string $leaveType, int $year): void
    {
        $balance = $this->ensureLeaveBalance($funsionariuId, $leaveType, $year);
        $used = 0;
        $pending = 0;

        $rows = $this->db->table('lisensa')
            ->where('funsionariu_id', $funsionariuId)
            ->where('tipu_lisensa', $leaveType)
            ->whereIn('estadu_lisensa', ['Aprovadu', 'Pendente'])
            ->where('data_hahu <=', $year . '-12-31')
            ->where('data_remata >=', $year . '-01-01')
            ->get()
            ->getResultArray();

        foreach ($rows as $row) {
            $days = $this->countLeaveDays($row['data_hahu'], $row['data_remata'], $year);
            if ($row['estadu_lisensa'] === 'Aprovadu') {
                $used += $days;
            } elseif ($row['estadu_lisensa'] === 'Pendente') {
                $pending += $days;
            }
        }

        $remaining = max(0, (float) $balance['entitlement_days'] - $used - $pending);
        $this->db->table('leave_balances')->where('id', $balance['id'])->update([
            'used_days' => $used,
            'pending_days' => $pending,
            'remaining_days' => $remaining,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function getSalariu($id = false, $funsionariu_id = false, $fulan = false, $tinan = false) {
        $builder = $this->db->table('salariu')
            ->select('salariu.*, funsionariu.naran_kompletu, funsionariu.nid')
            ->join('funsionariu', 'salariu.funsionariu_id = funsionariu.id');
        
        if ($id) return $builder->where('salariu.id', $id)->get()->getRowArray();
        if ($funsionariu_id) $builder->where('salariu.funsionariu_id', $funsionariu_id);
        if ($fulan) $builder->where('salariu.fulan', $fulan);
        if ($tinan) $builder->where('salariu.tinan', $tinan);
        
        return $builder->get()->getResultArray();
    }

    public function getSalariuDetalluBySalariuIds(array $salariuIds): array
    {
        $salariuIds = array_values(array_unique(array_filter(array_map('intval', $salariuIds))));
        if (empty($salariuIds)) {
            return [];
        }

        $rows = $this->db->table('salariu_detallu')
            ->whereIn('salariu_id', $salariuIds)
            ->orderBy('tipu', 'ASC')
            ->orderBy('id', 'ASC')
            ->get()
            ->getResultArray();

        $grouped = [];
        foreach ($rows as $row) {
            $grouped[(int) $row['salariu_id']][] = $row;
        }

        return $grouped;
    }

    public function getAvizu($id = false) {
        if ($id) return $this->db->table('avizu')->where('id', $id)->get()->getRowArray();

        $builder = $this->db->table('avizu');
        if ($this->db->fieldExists('data_remata', 'avizu')) {
            $builder->groupStart()
                ->where('data_remata', null)
                ->orWhere('data_remata >', date('Y-m-d H:i:s'))
                ->groupEnd();
        }

        return $builder->orderBy('data_publikasaun', 'DESC')->get()->getResultArray();
    }

    public function getTipuSansaun($id = false) {
        if ($id) return $this->db->table('tipu_sansaun')->where('id', $id)->get()->getRowArray();
        return $this->db->table('tipu_sansaun')->get()->getResultArray();
    }

    public function getSansaun($id = false, $funsionariu_id = false) {
        $builder = $this->db->table('sansaun')
            ->select('sansaun.*, funsionariu.naran_kompletu, funsionariu.nid, tipu_sansaun.naran_tipu, tipu_sansaun.kategoria, tipu_sansaun.valor_dedusaun AS tipu_valor')
            ->join('funsionariu', 'sansaun.funsionariu_id = funsionariu.id')
            ->join('tipu_sansaun', 'sansaun.tipu_sansaun_id = tipu_sansaun.id', 'left');
        
        if ($id) return $builder->where('sansaun.id', $id)->get()->getRowArray();
        if ($funsionariu_id) $builder->where('sansaun.funsionariu_id', $funsionariu_id);
        
        return $builder->orderBy('sansaun.created_at', 'DESC')->get()->getResultArray();
    }

    public function getAttendanceSettings() {
        $check = $this->db->table('attendance_settings')->get()->getRowArray();
        if (!$check) {
            $this->db->table('attendance_settings')->insert([
                'tama_hahu' => '08:00:00',
                'tama_remata' => '09:00:00',
                'sai_hahu' => '17:00:00',
                'sai_remata' => '18:00:00',
                'toleransia_minutu' => 15,
                'sabadu' => 0,
                'domingu' => 0,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            return $this->db->table('attendance_settings')->get()->getRowArray();
        }
        return $check;
    }

    public function saveData($table, $data) {
        return $this->db->table($table)->insert($data);
    }

    public function updateData($table, $data, $where) {
        return $this->db->table($table)->update($data, $where);
    }

    public function deleteData($table, $where) {
        return $this->db->table($table)->delete($where);
    }

    public function getAccessMenuCategory($role)
    {
        return $this->db->table('user_menu_category')
            ->select('*,user_menu_category.id AS menuCategoryID')
            ->join('user_access', 'user_menu_category.id = user_access.menu_category_id')
            ->where(['user_access.role_id' => $role])
            ->get()->getResultArray();
    }

    /**
     * Retrieves user access menu based on the role.
     *
     * @param int $role The ID of the user role.
     * @return array The access menu for the specified role.
     */
    public function getAccessMenu($role)
    {
        return $this->db->table('user_menu')
            ->join('user_access', 'user_menu.id = user_access.menu_id')
            ->where(['user_access.role_id' => $role])
            ->get()->getResultArray();
    }

    /**
     * Retrieves user roles.
     *
     * @param int|bool $role The ID of the role to retrieve, or false to retrieve all roles.
     * @return array The user role(s).
     */
    public function getUserRole($role = false)
    {
        if ($role) {
            return $this->db->table('user_role')->where(['id' => $role])->get()->getRowArray();
        }
        return $this->db->table('user_role')->get()->getResultArray();
    }

    /**
     * Creates a new user.
     *
     * @param array $dataUser Contains 'inputFullname', 'inputUsername', 'inputPassword', and 'inputRole'.
     * @return bool True on success, false on failure.
     */
    public function createUser($dataUser)
    {
        return $this->db->table('users')->insert([
            'fullname'    => $dataUser['inputFullname'],
            'username'    => $dataUser['inputUsername'],
            'password'    => password_hash($dataUser['inputPassword'], PASSWORD_DEFAULT),
            'role'        => $dataUser['inputRole'],
            'created_at'  => date('Y-m-d h:i:s')
        ]);
    }

    /**
     * Updates an existing user.
     *
     * @param array $dataUser Contains 'userID', 'inputFullname', 'inputUsername', 'inputPassword', and 'inputRole'.
     * @return bool True on success, false on failure.
     */
    public function updateUser($dataUser)
    {
        if ($dataUser['inputPassword']) {
            $password = password_hash($dataUser['inputPassword'], PASSWORD_DEFAULT);
        } else {
            $user         = $this->getUser(userID: $dataUser['userID']);
            $password     = $user['password'];
        }
        return $this->db->table('users')->update([
            'fullname'        => $dataUser['inputFullname'],
            'username'         => $dataUser['inputUsername'],
            'password'         => $password,
            'role'             => $dataUser['inputRole'],
        ], ['id' => $dataUser['userID']]);
    }

    /**
     * Deletes a user by their ID.
     *
     * @param int $userID The ID of the user to delete.
     * @return bool True on success, false on failure.
     */
    public function deleteUser($userID)
    {
        return $this->db->table('users')->delete(['id' => $userID]);
    }

    /**
     * Creates a new user role.
     *
     * @param array $dataRole Contains 'inputRoleName'.
     * @return bool True on success, false on failure.
     */
    public function createRole($dataRole)
    {
        return $this->db->table('user_role')->insert(['role_name' => $dataRole['inputRoleName']]);
    }

    /**
     * Updates an existing user role.
     *
     * @param array $dataRole Contains 'roleID' and 'inputRoleName'.
     * @return bool True on success, false on failure.
     */
    public function updateRole($dataRole)
    {
        return $this->db->table('user_role')->update(['role_name' => $dataRole['inputRoleName']], ['id' => $dataRole['roleID']]);
    }

    /**
     * Deletes a user role.
     *
     * @param int $role The ID of the role to delete.
     * @return bool True on success, false on failure.
     */
    public function deleteRole($role)
    {
        return $this->db->table('user_role')->delete(['id' => $role]);
    }

    /**
     * Checks if a user has access to a specific menu category.
     *
     * @param array $dataAccess Contains 'roleID' and 'menuCategoryID'.
     * @return int The count of access records.
     */
    public function checkUserMenuCategoryAccess($dataAccess)
    {
        return  $this->db->table('user_access')
            ->where([
                'role_id' => $dataAccess['roleID'],
                'menu_category_id' => $dataAccess['menuCategoryID']
            ])
            ->countAllResults();
    }

    /**
     * Checks if a user has access to a specific menu.
     *
     * @param array $dataAccess Contains 'roleID' and 'menuID'.
     * @return int The count of access records.
     */
    public function checkUserAccess($dataAccess)
    {
        return  $this->db->table('user_access')->where([
            'role_id' => $dataAccess['roleID'],
            'menu_id' => $dataAccess['menuID']
        ])->countAllResults();
    }

    /**
     * Checks if a user has access to a specific submenu.
     *
     * @param array $dataAccess Contains 'roleID' and 'submenuID'.
     * @return int The count of access records.
     */
    public function checkUserSubmenuAccess($dataAccess)
    {
        return  $this->db->table('user_access')->where([
            'role_id'       => $dataAccess['roleID'],
            'submenu_id'    => $dataAccess['submenuID']
        ])->countAllResults();
    }

    /**
     * Inserts a new menu category permission for a user role.
     *
     * @param array $dataAccess Contains 'roleID' and 'menuCategoryID'.
     * @return bool True on success, false on failure.
     */
    public function insertMenuCategoryPermission($dataAccess)
    {
        return $this->db->table('user_access')->insert(['role_id' => $dataAccess['roleID'], 'menu_category_id' => $dataAccess['menuCategoryID']]);
    }

    /**
     * Deletes a menu category permission for a user role.
     *
     * @param array $dataAccess Contains 'roleID' and 'menuCategoryID'.
     * @return bool True on success, false on failure.
     */
    public function deleteMenuCategoryPermission($dataAccess)
    {
        return $this->db->table('user_access')->delete(['role_id' => $dataAccess['roleID'], 'menu_category_id' => $dataAccess['menuCategoryID']]);
    }

    /**
     * Inserts a new menu permission for a user role.
     *
     * @param array $dataAccess Contains 'roleID' and 'menuID'.
     * @return bool True on success, false on failure.
     */
    public function insertMenuPermission($dataAccess)
    {
        return $this->db->table('user_access')->insert(['role_id' => $dataAccess['roleID'], 'menu_id' => $dataAccess['menuID']]);
    }

    /**
     * Deletes a menu permission for a user role.
     *
     * @param array $dataAccess Contains 'roleID' and 'menuID'.
     * @return bool True on success, false on failure.
     */
    public function deleteMenuPermission($dataAccess)
    {
        return $this->db->table('user_access')->delete(['role_id' => $dataAccess['roleID'], 'menu_id' => $dataAccess['menuID']]);
    }

    /**
     * Inserts a new submenu permission for a user role.
     *
     * @param array $dataAccess Contains 'roleID' and 'submenuID'.
     * @return bool True on success, false on failure.
     */
    public function insertSubmenuPermission($dataAccess)
    {
        return $this->db->table('user_access')->insert(['role_id' => $dataAccess['roleID'], 'submenu_id' => $dataAccess['submenuID']]);
    }

    /**
     * Deletes a submenu permission for a user role.
     *
     * @param array $dataAccess Contains 'roleID' and 'submenuID'.
     * @return bool True on success, false on failure.
     */
    public function deleteSubmenuPermission($dataAccess)
    {
        return $this->db->table('user_access')->delete(['role_id' => $dataAccess['roleID'], 'submenu_id' => $dataAccess['submenuID']]);
    }
    public function getSubsidiu($id = false) {
        if ($id) return $this->db->table('subsidiu')->where('id', $id)->get()->getRowArray();
        return $this->db->table('subsidiu')->get()->getResultArray();
    }

    public function getFunsionariuPaymentStatus($fulan, $tinan) {
        $fulan = (int) $fulan;
        $tinan = (int) $tinan;

        $data = $this->db->table('funsionariu')
            ->select('funsionariu.id, funsionariu.nid, funsionariu.naran_kompletu, pozisaun.naran_pozisaun, pozisaun.salariu_baziku, salariu.id AS salariu_id, salariu.estadu_pagamentu')
            ->join('pozisaun', 'funsionariu.pozisaun_id = pozisaun.id')
            ->join('salariu', 'funsionariu.id = salariu.funsionariu_id AND salariu.fulan = ' . $this->db->escape($fulan) . ' AND salariu.tinan = ' . $this->db->escape($tinan), 'left')
            ->get()->getResultArray();

        // Calculate Sanction Deductions for each employee (Active deductions that haven't been fully paid)
        foreach ($data as &$f) {
            $sansaun_dedusaun = $this->db->table('sansaun')
                ->selectSum('(valor_total - valor_pagadu)', 'total_restu')
                ->join('tipu_sansaun', 'sansaun.tipu_sansaun_id = tipu_sansaun.id')
                ->where('sansaun.funsionariu_id', $f['id'])
                ->where('sansaun.estadu_sansaun', 'Ativu')
                ->where('tipu_sansaun.kategoria', 'Korta Saláriu')
                ->where('valor_pagadu < valor_total')
                ->get()->getRowArray();
            
            $f['sansaun_dedusaun'] = $sansaun_dedusaun['total_restu'] ?? 0;
        }

        return $data;
    }
}
