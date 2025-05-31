<?php

namespace Tests\Feature;

use Alison\ProjectManagementAssistant\Actions\Fortify\CreateNewUser;
use Alison\ProjectManagementAssistant\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class CreateNewUserTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Створюємо ролі для тестування
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
    }

    public function test_user_can_be_created_with_valid_data(): void
    {
        $createNewUser = new CreateNewUser();

        $user = $createNewUser->create([
            'name' => 'testuser',
            'email' => 'test.student@student.uzhnu.edu.ua',
            'password' => 'password',
            'password_confirmation' => 'password',
            'course_number' => 2,
            'terms' => true,
        ]);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('testuser', $user->name);
        $this->assertEquals('test.student@student.uzhnu.edu.ua', $user->email);
        $this->assertEquals('Test', $user->first_name);
        $this->assertEquals('Student', $user->last_name);
        $this->assertNull($user->middle_name);
        $this->assertEquals(2, $user->course_number);
        $this->assertTrue($user->hasRole('student'));
    }

    public function test_user_can_be_created_with_middle_name_in_email(): void
    {
        $createNewUser = new CreateNewUser();

        $user = $createNewUser->create([
            'name' => 'testuser',
            'email' => 'test.student.middle@student.uzhnu.edu.ua',
            'password' => 'password',
            'password_confirmation' => 'password',
            'course_number' => 2,
            'terms' => true,
        ]);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('Test', $user->first_name);
        $this->assertEquals('Student', $user->last_name);
        $this->assertEquals('Middle', $user->middle_name);
    }

    public function test_user_can_override_auto_filled_names(): void
    {
        $createNewUser = new CreateNewUser();

        $user = $createNewUser->create([
            'name' => 'testuser',
            'email' => 'test.student@student.uzhnu.edu.ua',
            'password' => 'password',
            'password_confirmation' => 'password',
            'first_name' => 'Custom',
            'last_name' => 'Name',
            'middle_name' => 'Override',
            'course_number' => 2,
            'terms' => true,
        ]);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('Custom', $user->first_name);
        $this->assertEquals('Name', $user->last_name);
        $this->assertEquals('Override', $user->middle_name);
    }

    public function test_teacher_role_is_assigned_for_teacher_domain(): void
    {
        $createNewUser = new CreateNewUser();

        $user = $createNewUser->create([
            'name' => 'testteacher',
            'email' => 'test.teacher@uzhnu.edu.ua',
            'password' => 'password',
            'password_confirmation' => 'password',
            'terms' => true,
        ]);

        $this->assertInstanceOf(User::class, $user);
        $this->assertTrue($user->hasRole('teacher'));
        $this->assertNull($user->course_number);
    }

    public function test_invalid_domain_throws_exception(): void
    {
        $this->expectException(ValidationException::class);

        $createNewUser = new CreateNewUser();

        $createNewUser->create([
            'name' => 'testuser',
            'email' => 'test.user@gmail.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'terms' => true,
        ]);
    }
}
