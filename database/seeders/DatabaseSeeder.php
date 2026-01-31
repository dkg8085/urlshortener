<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Company;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create roles
        $roles = ['superadmin', 'admin', 'member', 'sales', 'manager'];
        foreach ($roles as $roleName) {
            Role::create(['name' => $roleName, 'guard_name' => 'web']);
        }

        // Create permissions
        $permissions = [
            'create short_url',
            'view short_url',
            'edit short_url',
            'delete short_url',
            'invite users',
        ];
        
        foreach ($permissions as $permissionName) {
            Permission::create(['name' => $permissionName, 'guard_name' => 'web']);
        }

        // Assign permissions to roles
        $adminRole = Role::findByName('admin');
        $adminRole->givePermissionTo(['view short_url', 'invite users']);
        
        $salesRole = Role::findByName('sales');
        $salesRole->givePermissionTo(['create short_url', 'view short_url']);
        
        $managerRole = Role::findByName('manager');
        $managerRole->givePermissionTo(['create short_url', 'view short_url', 'edit short_url', 'delete short_url']);

        // Create SuperAdmin using raw SQL as required
        $this->createSuperAdminWithRawSQL();

        // Create companies
        $company1 = Company::create([
            'name' => 'Tech Solutions Inc.',
            'slug' => 'tech-solutions',
            'is_active' => true,
        ]);

        $company2 = Company::create([
            'name' => 'Marketing Pro Agency',
            'slug' => 'marketing-pro',
            'is_active' => true,
        ]);

        // Create admin user
        $admin = User::create([
            'name' => 'John Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
            'company_id' => $company1->id,
            'email_verified_at' => now(),
            'status' => 'active'
        ]);
        $admin->assignRole('admin');

        // Create sales user
        $sales = User::create([
            'name' => 'Sarah Sales',
            'email' => 'sales@example.com',
            'password' => Hash::make('password123'),
            'company_id' => $company1->id,
            'email_verified_at' => now(),
            'status' => 'active'
        ]);
        $sales->assignRole('sales');

        // Create manager user
        $manager = User::create([
            'name' => 'Mike Manager',
            'email' => 'manager@example.com',
            'password' => Hash::make('password123'),
            'company_id' => $company1->id,
            'email_verified_at' => now(),
            'status' => 'active'
        ]);
        $manager->assignRole('manager');

        // Create member user
        $member = User::create([
            'name' => 'Emma Member',
            'email' => 'member@example.com',
            'password' => Hash::make('password123'),
            'company_id' => $company2->id,
            'email_verified_at' => now(),
            'status' => 'active'
        ]);
        $member->assignRole('member');

        // Create sample short URLs
        \App\Models\ShortUrl::create([
            'user_id' => $sales->id,
            'company_id' => $company1->id,
            'title' => 'Product Launch Page',
            'original_url' => 'https://techsolutions.com/product-launch-2024',
            'short_code' => 'launch24',
            'clicks' => 150,
            'is_active' => true,
        ]);

        \App\Models\ShortUrl::create([
            'user_id' => $manager->id,
            'company_id' => $company1->id,
            'title' => 'Company About Page',
            'original_url' => 'https://techsolutions.com/about-us/company-history',
            'short_code' => 'aboutus',
            'clicks' => 89,
            'is_active' => true,
        ]);

        \App\Models\ShortUrl::create([
            'user_id' => $member->id,
            'company_id' => $company2->id,
            'title' => 'Marketing Campaign',
            'original_url' => 'https://marketingpro.com/campaign/summer-sale',
            'short_code' => 'summer24',
            'clicks' => 245,
            'is_active' => true,
            'expires_at' => now()->addDays(30),
        ]);
    }

    private function createSuperAdminWithRawSQL(): void
    {
        // Create SuperAdmin user using raw SQL as required
        DB::statement("
            INSERT INTO users (name, email, password, email_verified_at, created_at, updated_at, status) 
            VALUES ('Super Admin', 'superadmin@example.com', ?, NOW(), NOW(), NOW(), 'active')
        ", [Hash::make('password123')]);

        $userId = DB::getPdo()->lastInsertId();
        $roleId = DB::table('roles')->where('name', 'superadmin')->value('id');

        // Assign role using raw SQL
        DB::statement("
            INSERT INTO model_has_roles (role_id, model_type, model_id) 
            VALUES (?, 'App\\\\Models\\\\User', ?)
        ", [$roleId, $userId]);
    }
}