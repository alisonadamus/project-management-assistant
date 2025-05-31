<?php

namespace Tests\Feature;

use Alison\ProjectManagementAssistant\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\GoogleProvider;
use Laravel\Socialite\Two\User as SocialiteUser;
use Mockery;
use Tests\TestCase;

class GoogleAuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Створюємо ролі для тестування
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
    }

    public function test_redirect_to_google(): void
    {
        $response = $this->get(route('google.redirect'));

        $response->assertRedirect();
        $this->assertStringContainsString('accounts.google.com', $response->getTargetUrl());
    }

    public function test_google_callback_creates_user_with_student_role(): void
    {
        // Створюємо мок об'єкт користувача Google
        $abstractUser = Mockery::mock(SocialiteUser::class);
        $abstractUser->shouldReceive('getId')->andReturn('123456789');
        $abstractUser->shouldReceive('getEmail')->andReturn('test.student@student.uzhnu.edu.ua');
        $abstractUser->shouldReceive('getNickname')->andReturn('teststudent');
        $abstractUser->shouldReceive('getAvatar')->andReturn('https://example.com/avatar.jpg');
        $abstractUser->shouldReceive('getName')->andReturn('Test Student');
        $abstractUser->user = [
            'given_name' => 'Test',
            'family_name' => 'Student',
        ];

        // Мокуємо провайдер Google
        $provider = Mockery::mock(GoogleProvider::class);
        $provider->shouldReceive('user')->andReturn($abstractUser);

        // Мокуємо фасад Socialite
        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        // Викликаємо callback
        $response = $this->get(route('google.callback'));

        // Перевіряємо, що користувач був створений
        $this->assertDatabaseHas('users', [
            'email' => 'test.student@student.uzhnu.edu.ua',
            'google_id' => '123456789',
        ]);

        // Перевіряємо, що користувачу призначена роль студента
        $user = User::where('email', 'test.student@student.uzhnu.edu.ua')->first();
        $this->assertTrue($user->hasRole('student'));

        // Перевіряємо перенаправлення на dashboard
        $response->assertRedirect(route('dashboard'));
    }

    public function test_google_callback_creates_user_with_teacher_role(): void
    {
        // Створюємо мок об'єкт користувача Google
        $abstractUser = Mockery::mock(SocialiteUser::class);
        $abstractUser->shouldReceive('getId')->andReturn('987654321');
        $abstractUser->shouldReceive('getEmail')->andReturn('test.teacher@uzhnu.edu.ua');
        $abstractUser->shouldReceive('getNickname')->andReturn('testteacher');
        $abstractUser->shouldReceive('getAvatar')->andReturn('https://example.com/avatar.jpg');
        $abstractUser->shouldReceive('getName')->andReturn('Test Teacher');
        $abstractUser->user = [
            'given_name' => 'Test',
            'family_name' => 'Teacher',
        ];

        // Мокуємо провайдер Google
        $provider = Mockery::mock(GoogleProvider::class);
        $provider->shouldReceive('user')->andReturn($abstractUser);

        // Мокуємо фасад Socialite
        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        // Викликаємо callback
        $response = $this->get(route('google.callback'));

        // Перевіряємо, що користувач був створений
        $this->assertDatabaseHas('users', [
            'email' => 'test.teacher@uzhnu.edu.ua',
            'google_id' => '987654321',
        ]);

        // Перевіряємо, що користувачу призначена роль викладача
        $user = User::where('email', 'test.teacher@uzhnu.edu.ua')->first();
        $this->assertTrue($user->hasRole('teacher'));

        // Перевіряємо перенаправлення на dashboard
        $response->assertRedirect(route('dashboard'));
    }

    public function test_google_callback_rejects_invalid_domain(): void
    {
        // Створюємо мок об'єкт користувача Google з неприпустимим доменом
        $abstractUser = Mockery::mock(SocialiteUser::class);
        $abstractUser->shouldReceive('getId')->andReturn('111222333');
        $abstractUser->shouldReceive('getEmail')->andReturn('test.user@gmail.com');
        $abstractUser->shouldReceive('getNickname')->andReturn('testuser');
        $abstractUser->shouldReceive('getAvatar')->andReturn('https://example.com/avatar.jpg');
        $abstractUser->shouldReceive('getName')->andReturn('Test User');
        $abstractUser->user = [
            'given_name' => 'Test',
            'family_name' => 'User',
        ];

        // Мокуємо провайдер Google
        $provider = Mockery::mock(GoogleProvider::class);
        $provider->shouldReceive('user')->andReturn($abstractUser);

        // Мокуємо фасад Socialite
        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        // Викликаємо callback
        $response = $this->get(route('google.callback'));

        // Перевіряємо, що користувач НЕ був створений
        $this->assertDatabaseMissing('users', [
            'email' => 'test.user@gmail.com',
        ]);

        // Перевіряємо перенаправлення на сторінку входу з помилкою
        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('email');
    }
}
