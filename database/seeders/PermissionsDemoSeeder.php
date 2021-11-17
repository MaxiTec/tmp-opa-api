<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Role;
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

        //Permisos de Propiedades
        // Permission::create(['name' => 'audit property']);
        Permission::create(['name' => 'index property']);
        Permission::create(['name' => 'show property']);
        Permission::create(['name' => 'update property']);
        Permission::create(['name' => 'create property']);
        Permission::create(['name' => 'delete property']);
        // Permisos de auditorias
        Permission::create(['name' => 'create audit']); 
        Permission::create(['name' => 'index audit']); 
        Permission::create(['name' => 'show audit']); 
        Permission::create(['name' => 'update audit']); 
        Permission::create(['name' => 'delete audit']); 

        Permission::create(['name' => 'publish audit']); //complete Audit
        Permission::create(['name' => 'unpublish audit']); // Undo Audit


        // User Permissions
        Permission::create(['name' => 'index user']);
        Permission::create(['name' => 'show user']);
        Permission::create(['name' => 'create user']);
        Permission::create(['name' => 'delete user']);
        Permission::create(['name' => 'update user']);
        

        // create roles and assign existing permissions
        $role1 = Role::create(['name' => 'administrator']);
        $role1->givePermissionTo('index property');
        $role1->givePermissionTo('show property');
        $role1->givePermissionTo('update property');
        $role1->givePermissionTo('create property');
        $role1->givePermissionTo('delete property');

        $role1->givePermissionTo('create audit');
        $role1->givePermissionTo('index audit');
        $role1->givePermissionTo('show audit');
        $role1->givePermissionTo('update audit');
        $role1->givePermissionTo('delete audit');
        $role1->givePermissionTo('publish audit');
        $role1->givePermissionTo('unpublish audit');

        $role2 = Role::create(['name' => 'auditor']);
        $role2->givePermissionTo('index property');
        $role2->givePermissionTo('show property');
        $role2->givePermissionTo('show audit');
        $role2->givePermissionTo('update audit');

        $role3 = Role::create(['name' => 'property']);
        $role3->givePermissionTo('publish audit');
        $role3->givePermissionTo('unpublish audit');

        $role4 = Role::create(['name' => 'super-admin']);
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
        $user->assignRole($role3);

        $user = \App\Models\User::factory()->create([
            'name' => 'Example Super-Admin User',
            'email' => 'superadmin@oasis.com',
        ]);
        $user->assignRole($role4);
    }
}
