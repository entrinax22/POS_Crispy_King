<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = Role::create(['name' => 'admin']);
        $cashier = Role::create(['name' => 'cashier']);
        $customer = Role::create(['name' => 'customer']);
        Permission::create(['name' => 'manage_products']);
        Permission::create(['name' => 'make_sales']);
        Permission::create(['name' => 'buy_products']);

        $admin->givePermissionTo(['manage_products', 'make_sales']);
        $cashier->givePermissionTo('make_sales');
        $customer->givePermissionTo('buy_products');
    }
}
