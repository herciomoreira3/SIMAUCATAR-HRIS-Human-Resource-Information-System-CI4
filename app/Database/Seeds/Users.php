<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class Users extends Seeder
{
	public function run()
	{
		// Database seeding for user menu category
		$this->db->table('user_menu_category')->insertBatch([
			[
				'menu_category' 	=> 'Common Page'
			],
			[
				'menu_category' 	=> 'Settings'
			]
		]);

		// Database seeding for user menu
		$this->db->table('user_menu')->insertBatch([
			[
				'menu_category' => 1,
				'title' 		=> 'Dashboard',
				'url'    		=> 'dashboard',
				'icon'    		=> 'home',
				'parent'   		=> 0
			],
			[
				'menu_category' => 2,
				'title' 		=> 'Users',
				'url'    		=> 'users',
				'icon'    		=> 'user',
				'parent'   		=> 0
			],
			[
				'menu_category' => 2,
				'title' 		=> 'Menu Management',
				'url'    		=> 'menu-management',
				'icon'    		=> 'command',
				'parent'   		=> 0
			],
		]);

		// Database seeding for user role
		$this->db->table('user_role')->insert([
			'id'    			=>  1,
			'role_name'    		=>  'Developer'
		]);

		// Database seeding for users
		$this->db->table('users')->insert([
			'fullname' 		=> 'Developer',
			'username'    	=> 'developer@mail.io',
			'password'    	=>  password_hash('123456', PASSWORD_DEFAULT),
			'role'    		=>  1,
			'created_at'    =>  date('Y-m-d h:i:s')
		]);

		// Database seeding for user access
		$this->db->table('user_access')->insertBatch([
			[
				'role_id'    		=>  1,
				'menu_category_id'  =>  1,
				'menu_id'    		=>  0,
				'submenu_id'		=> 	0
			],
			[
				'role_id'    		=>  1,
				'menu_category_id'  =>  0,
				'menu_id'    		=>  1,
				'submenu_id'		=> 	0
			],
			[
				'role_id'    		=>  1,
				'menu_category_id'  =>  2,
				'menu_id'    		=>  0,
				'submenu_id'		=> 	0
			],
			[
				'role_id'    		=>  1,
				'menu_category_id'  =>  0,
				'menu_id'    		=>  2,
				'submenu_id'		=> 	0
			],
			[
				'role_id'    		=>  1,
				'menu_category_id'  =>  0,
				'menu_id'    		=>  3,
				'submenu_id'		=> 	0
			],
		]);
	}
}
