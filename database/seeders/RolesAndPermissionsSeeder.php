<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $collection = collect([
            'users',
            'vendors',
            'categories',
            'products',
            'languages',
            'faqs',
            'orders',
            'quotes',
            'countries',
            'cities',
            'contacts',
            'subscribers',
            'subscribes',
            'tags',
            'currencies',
            'memberships',
            'pages',
            'teams',
            'inquires',
            'coupons'
        ]);

        $collection->each(function ($item, $key) {
            // create permissions for each collection item
            Permission::create(['name' => 'viewAny ' . $item, 'guard_name' => 'admin']);
            Permission::create(['name' => 'view ' . $item, 'guard_name' => 'admin']);
            Permission::create(['name' => 'create ' . $item, 'guard_name' => 'admin']);
            Permission::create(['name' => 'update ' . $item, 'guard_name' => 'admin']);
            Permission::create(['name' => 'delete ' . $item, 'guard_name' => 'admin']);
            Permission::create(['name' => 'restore ' . $item, 'guard_name' => 'admin']);
            Permission::create(['name' => 'forceDelete ' . $item, 'guard_name' => 'admin']);
        });
        Permission::create(['name' => 'seo', 'guard_name' => 'admin']);

        // Create a Super Admin Role and assign all Permissions
        $role = Role::create(['name' => 'Super Admin', 'guard_name' => 'admin']);
        $role->givePermissionTo(Permission::all());

        // Give User Super Admin Role
        $admin = Admin::whereEmail('superAdmin@youmats.com')->first();

        if(!$admin) {
            $admin = Admin::create([
                'name' => 'SuperAdmin',
                'email' => 'superAdmin@youmats.com',
                'password' => bcrypt('123456')
            ]);
        }
        $admin->assignRole('Super Admin');
    }
}
