<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RegisterRoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_page_loads_successfully(): void
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
        $response->assertSee('Daftar Sebagai');
        $response->assertSee('Siswa');
        $response->assertSee('Wali Kelas');
    }

    public function test_student_registration_assigns_siswa_role_and_stores_nisn(): void
    {
        $data = [
            'role_type' => 'siswa',
            'name' => 'Siswa Baru',
            'class' => 'Kelas 4',
            'nisn' => '1234567890',
            'birth_date' => '2016-01-01',
            'username' => 'siswabaru',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ];

        $response = $this->post('/register', $data);

        $response->assertRedirect('/dashboard');

        $user = User::where('username', 'siswabaru')->first();
        $this->assertNotNull($user);
        $this->assertEquals('siswa', $user->role);
        $this->assertEquals('1234567890', $user->nisn);
        $this->assertNull($user->nip);
        $this->assertEquals('Kelas 4', $user->class);

        $user->delete();
    }

    public function test_teacher_registration_assigns_admin_role_and_stores_nip(): void
    {
        $data = [
            'role_type' => 'wali_kelas',
            'name' => 'Guru Baru',
            'class' => 'Wali Kelas 5',
            'nip' => '198765432101234567',
            'birth_date' => '1988-02-02',
            'username' => 'gurubaru',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ];

        $response = $this->post('/register', $data);

        $response->assertRedirect('/dashboard');

        $user = User::where('username', 'gurubaru')->first();
        $this->assertNotNull($user);
        $this->assertEquals('admin', $user->role);
        $this->assertEquals('198765432101234567', $user->nip);
        $this->assertNull($user->nisn);
        $this->assertEquals('Wali Kelas 5', $user->class);

        $user->delete();
    }
}
