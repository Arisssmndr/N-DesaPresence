<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\Livewire;
use App\Livewire\AdminProfilManager;

class AdminProfilManagerTest extends TestCase
{
    public function test_login_page_does_not_contain_test_credentials_and_has_staff_link(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertDontSee('Kredensial Pengujian (Seeder Defaults)');
        $response->assertDontSee('admin123');
        $response->assertSee('/staf/login');
    }

    public function test_admin_can_access_profile_page(): void
    {
        $admin = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/pengaturan-profil');
        $response->assertStatus(200);
        $response->assertSeeLivewire(AdminProfilManager::class);
    }

    public function test_admin_can_update_profile_and_username(): void
    {
        $unique = Str::lower(Str::random(8));
        $user = User::factory()->create([
            'name' => 'Original Admin',
            'username' => 'orig_' . $unique,
            'email' => 'orig_' . $unique . '@example.com',
            'role' => 'admin',
            'password' => Hash::make('password123'),
        ]);

        $newUsername = 'upd_' . $unique;
        $newEmail = 'upd_' . $unique . '@example.com';

        Livewire::actingAs($user)
            ->test(AdminProfilManager::class)
            ->set('name', 'Updated Admin Name')
            ->set('username', $newUsername)
            ->set('email', $newEmail)
            ->call('updateProfil')
            ->assertHasNoErrors()
            ->assertDispatched('notify');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Admin Name',
            'username' => $newUsername,
            'email' => $newEmail,
        ]);
        $user->delete();
    }

    public function test_admin_can_update_password_with_correct_current_password(): void
    {
        $unique = Str::lower(Str::random(8));
        $user = User::factory()->create([
            'username' => 'pass_' . $unique,
            'email' => 'pass_' . $unique . '@example.com',
            'role' => 'admin',
            'password' => Hash::make('oldpassword123'),
        ]);

        Livewire::actingAs($user)
            ->test(AdminProfilManager::class)
            ->set('password_saat_ini', 'oldpassword123')
            ->set('password_baru', 'newsecret123')
            ->set('password_baru_confirmation', 'newsecret123')
            ->call('updatePassword')
            ->assertHasNoErrors()
            ->assertDispatched('notify');

        $this->assertTrue(Hash::check('newsecret123', $user->fresh()->password));
        $user->delete();
    }

    public function test_update_password_fails_if_current_password_is_wrong(): void
    {
        $unique = Str::lower(Str::random(8));
        $user = User::factory()->create([
            'username' => 'wrong_' . $unique,
            'email' => 'wrong_' . $unique . '@example.com',
            'role' => 'admin',
            'password' => Hash::make('correctpassword'),
        ]);

        Livewire::actingAs($user)
            ->test(AdminProfilManager::class)
            ->set('password_saat_ini', 'wrongpassword')
            ->set('password_baru', 'newsecret123')
            ->set('password_baru_confirmation', 'newsecret123')
            ->call('updatePassword')
            ->assertHasErrors(['password_saat_ini']);

        $this->assertFalse(Hash::check('newsecret123', $user->fresh()->password));
        $user->delete();
    }
}

