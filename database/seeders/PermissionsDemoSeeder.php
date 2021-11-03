<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionsDemoSeeder extends Seeder
{
    /**
     * Create the initial roles and permissions.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // create permissions
        //Permisos de Hoteles
        // Permission::create(['name' => 'audit property']);
        Permission::create(['name' => 'view property']);
        Permission::create(['name' => 'edit property']);
        Permission::create(['name' => 'add property']);
        Permission::create(['name' => 'delete property']);

        Permission::create(['name' => 'add audit']); 
        Permission::create(['name' => 'view audit']); 
        Permission::create(['name' => 'edit audit']); 
        Permission::create(['name' => 'delete audit']); 
        Permission::create(['name' => 'publish audit']); //complete Audit
        Permission::create(['name' => 'unpublish audit']); // Undo Audit
        // User Permissions
        Permission::create(['name' => 'view user']);
        Permission::create(['name' => 'add user']);
        Permission::create(['name' => 'edit user']);
        Permission::create(['name' => 'delete user']);
        Permission::create(['name' => 'update user']);
        

        // create roles and assign existing permissions
        $role1 = Role::create(['name' => 'administrator']);
        $role1->givePermissionTo('view property');
        $role1->givePermissionTo('edit property');
        $role1->givePermissionTo('add property');
        $role1->givePermissionTo('delete property');
        $role1->givePermissionTo('add audit');
        $role1->givePermissionTo('view audit');
        $role1->givePermissionTo('edit audit');
        $role1->givePermissionTo('delete audit');
        $role1->givePermissionTo('publish audit');
        $role1->givePermissionTo('unpublish audit');

        $role2 = Role::create(['name' => 'auditor']);
        $role2->givePermissionTo('view property');
        $role2->givePermissionTo('view audit');
        $role2->givePermissionTo('view audit');
        $role2->givePermissionTo('edit audit');

        $role3 = Role::create(['name' => 'property']);

        $role4 = Role::create(['name' => 'Super-Admin']);
        // gets all permissions via Gate::before rule; see AuthServiceProvider

        // create demo users
        $user = \App\Models\User::factory()->create([
            'name' => 'Example Admin User',
            'email' => 'admin@oasis.com',
        ]);
        $user->assignRole($role1);

        $user = \App\Models\User::factory()->create([
            'name' => 'Example Audit User',
            'email' => 'audit@oasis.com',
        ]);
        $user->assignRole($role2);

        $user = \App\Models\User::factory()->create([
            'name' => 'Example Property User',
            'email' => 'property@oasis.com',
        ]);
        $user->assignRole($role2);

        $user = \App\Models\User::factory()->create([
            'name' => 'Example Super-Admin User',
            'email' => 'superadmin@oasis.com',
        ]);
        $user->assignRole($role4);
    }
}
