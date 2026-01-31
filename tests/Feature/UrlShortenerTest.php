<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use App\Models\ShortUrl;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UrlShortenerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->createRoles();
    }

    private function createRoles()
    {
        \Spatie\Permission\Models\Role::create(['name' => 'superadmin']);
        \Spatie\Permission\Models\Role::create(['name' => 'admin']);
        \Spatie\Permission\Models\Role::create(['name' => 'member']);
        \Spatie\Permission\Models\Role::create(['name' => 'sales']);
        \Spatie\Permission\Models\Role::create(['name' => 'manager']);
    }

    private function createCompany($name)
    {
        return Company::create([
            'name' => $name,
            'slug' => strtolower(str_replace(' ', '-', $name)),
            'is_active' => true,
        ]);
    }

    private function createUser($company, $name, $email, $role)
    {
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => bcrypt('password123'),
            'company_id' => $company->id,
            'email_verified_at' => now(),
            'status' => 'active',
        ]);
        $user->assignRole($role);
        return $user;
    }

    private function createShortUrl($user, $company, $url, $code)
    {
        return ShortUrl::create([
            'user_id' => $user->id,
            'company_id' => $company->id,
            'original_url' => $url,
            'short_code' => $code,
            'clicks' => 0,
            'is_active' => true,
        ]);
    }

    
    public function admin_and_member_cannot_create_short_urls()
    {
        $company = $this->createCompany('Test Company');
        
        $admin = $this->createUser($company, 'Admin User', 'admin@test.com', 'admin');
        $member = $this->createUser($company, 'Member User', 'member@test.com', 'member');
        
        
        $this->actingAs($admin);
        $response = $this->get(route('short-urls.create'));
        $response->assertStatus(403);
        
        $this->actingAs($member);
        $response = $this->get(route('short-urls.create'));
        $response->assertStatus(403);
    }

    
    public function superadmin_cannot_create_short_urls()
    {
        $superadmin = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@test.com',
            'password' => bcrypt('password123'),
            'email_verified_at' => now(),
            'status' => 'active',
        ]);
        $superadmin->assignRole('superadmin');
        
        $this->actingAs($superadmin);
        $response = $this->get(route('short-urls.create'));
        $response->assertStatus(403);
    }

    
    public function sales_and_manager_can_create_short_urls()
    {
        $company = $this->createCompany('Sales Company');
        
        $sales = $this->createUser($company, 'Sales User', 'sales@test.com', 'sales');
        $manager = $this->createUser($company, 'Manager User', 'manager@test.com', 'manager');
        
        
        $this->actingAs($sales);
        $response = $this->get(route('short-urls.create'));
        $response->assertStatus(200);
        
       
        $response = $this->post(route('short-urls.store'), [
            'original_url' => 'https://example-sales.com'
        ]);
        $response->assertRedirect(route('short-urls.index'));
        
        
        $this->actingAs($manager);
        $response = $this->get(route('short-urls.create'));
        $response->assertStatus(200);
        
        
        $response = $this->post(route('short-urls.store'), [
            'original_url' => 'https://example-manager.com'
        ]);
        $response->assertRedirect(route('short-urls.index'));
    }

    
    public function admin_can_only_see_short_urls_not_from_their_own_company()
    {
        $company1 = $this->createCompany('Company A');
        $company2 = $this->createCompany('Company B');
        
        $admin = $this->createUser($company1, 'Admin User', 'admin@companya.com', 'admin');
        
        
        $user1 = $this->createUser($company1, 'User 1', 'user1@companya.com', 'sales');
        $user2 = $this->createUser($company2, 'User 2', 'user2@companyb.com', 'sales');
        
        $this->createShortUrl($user1, $company1, 'https://company-a-url.com', 'companya');
        $this->createShortUrl($user2, $company2, 'https://company-b-url.com', 'companyb');
        
        $this->actingAs($admin);
        $response = $this->get(route('short-urls.index'));
        
        
        $response->assertSee('https://company-b-url.com');
        $response->assertDontSee('https://company-a-url.com');
    }

    
    public function member_can_only_see_short_urls_not_created_by_themselves()
    {
        $company = $this->createCompany('Test Company');
        
        $member1 = $this->createUser($company, 'Member One', 'member1@test.com', 'member');
        $member2 = $this->createUser($company, 'Member Two', 'member2@test.com', 'member');
        
        $sales = $this->createUser($company, 'Sales User', 'sales@test.com', 'sales');
        
        
        $this->createShortUrl($member1, $company, 'https://member1-url.com', 'member1');
        $this->createShortUrl($member2, $company, 'https://member2-url.com', 'member2');
        $this->createShortUrl($sales, $company, 'https://sales-url.com', 'salesurl');
        
        $this->actingAs($member1);
        $response = $this->get(route('short-urls.index'));
        
        $response->assertSee('https://member2-url.com');
        $response->assertSee('https://sales-url.com');
        $response->assertDontSee('https://member1-url.com');
    }

    public function short_urls_are_not_publicly_resolvable()
    {
        $company = $this->createCompany('Test Company');
        $sales = $this->createUser($company, 'Sales User', 'sales@test.com', 'sales');
        
        $shortUrl = ShortUrl::create([
            'user_id' => $sales->id,
            'company_id' => $company->id,
            'original_url' => 'https://example.com',
            'short_code' => 'test123',
            'clicks' => 0,
            'is_active' => true,
        ]);
        
        
        $response = $this->get(route('short-urls.redirect', 'test123'));
        $response->assertRedirect(route('login'));
        
        
        $this->actingAs($sales);
        $response = $this->get(route('short-urls.redirect', 'test123'));
        $response->assertRedirect($shortUrl->original_url);
    }

    
    public function superadmin_cannot_see_short_urls_list()
    {
        $superadmin = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@test.com',
            'password' => bcrypt('password123'),
            'email_verified_at' => now(),
            'status' => 'active',
        ]);
        $superadmin->assignRole('superadmin');
        
        $company = $this->createCompany('Test Company');
        $sales = $this->createUser($company, 'Sales User', 'sales@test.com', 'sales');
        
        $this->createShortUrl($sales, $company, 'https://test-url.com', 'testurl');
        
        $this->actingAs($superadmin);
        $response = $this->get(route('short-urls.index'));
        
        
        $response->assertDontSee('Short Code');
        $response->assertDontSee('https://test-url.com');
    }

    
    public function sales_can_only_see_urls_from_their_company()
    {
        $company1 = $this->createCompany('Company 1');
        $company2 = $this->createCompany('Company 2');
        
        $sales = $this->createUser($company1, 'Sales User', 'sales@company1.com', 'sales');
        
        $sales2 = $this->createUser($company2, 'Sales 2', 'sales2@company2.com', 'sales');
        

        $this->createShortUrl($sales, $company1, 'https://company1-url.com', 'comp1');
        $this->createShortUrl($sales2, $company2, 'https://company2-url.com', 'comp2');
        
        $this->actingAs($sales);
        $response = $this->get(route('short-urls.index'));
        
        $response->assertSee('https://company1-url.com');
        $response->assertDontSee('https://company2-url.com');
    }
}