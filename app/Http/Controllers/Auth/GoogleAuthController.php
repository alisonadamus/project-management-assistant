<?php

namespace Alison\ProjectManagementAssistant\Http\Controllers\Auth;

use Alison\ProjectManagementAssistant\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    /**
     * Перенаправлення користувача на сторінку авторизації Google.
     *
     * @return RedirectResponse
     */
    public function redirectToGoogle(): RedirectResponse
    {
        // Додаємо логування для дебагу
        \Log::info('Google redirect URL: ' . config('services.google.redirect'));

        return Socialite::driver('google')->redirect();
    }

    /**
     * Обробка відповіді від Google після авторизації.
     *
     * @return RedirectResponse
     */
    public function handleGoogleCallback(): RedirectResponse
    {
        try {
            // Додаємо логування для дебагу
            \Log::info('Google callback URL: ' . request()->fullUrl());

            $googleUser = Socialite::driver('google')->user();

            // Перевірка домену електронної пошти
            $email = $googleUser->getEmail();
            $allowedDomains = ['student.uzhnu.edu.ua', 'uzhnu.edu.ua'];
            $isAllowedDomain = false;

            foreach ($allowedDomains as $domain) {
                if (Str::endsWith($email, '@' . $domain)) {
                    $isAllowedDomain = true;
                    break;
                }
            }

            if (!$isAllowedDomain) {
                return redirect()->route('login')
                    ->withErrors(['email' => 'Дозволена реєстрація лише з доменами @student.uzhnu.edu.ua або @uzhnu.edu.ua']);
            }

            // Пошук або створення користувача
            $user = User::query()->where('google_id', $googleUser->getId())->orWhere('email', $email)->first();

            if (!$user) {
                // Розбір імені з email або даних Google-акаунта
                $nameParts = $this->extractNameFromEmail($email, $googleUser->user ?? null);

                // Визначення ролі на основі домену
                $role = Str::endsWith($email, '@student.uzhnu.edu.ua') ? 'student' : 'teacher';

                // Створення нового користувача
                $user = User::query()->create([
                    'name' => $googleUser->getNickname() ?? Str::before($email, '@'),
                    'email' => $email,
                    'google_id' => $googleUser->getId(),
                    'password' => Hash::make(Str::random(16)),
                    'first_name' => $nameParts['first_name'],
                    'last_name' => $nameParts['last_name'],
                    'middle_name' => $nameParts['middle_name'],
                    'avatar' => $googleUser->getAvatar(),
                    'email_verified_at' => now(),
                ]);

                // Встановлення номеру курсу для студентів
                if ($role === 'student') {
                    $user->course_number = rand(1, 4); // Тимчасово випадковий курс
                    $user->save();
                }

                // Призначення ролі, перевіряємо чи існує роль
                try {
                    // Перевіряємо, чи існує роль
                    if (\Spatie\Permission\Models\Role::where('name', $role)->exists()) {
                        $user->assignRole($role);
                    } else {
                        \Log::error("Роль '$role' не існує в базі даних");
                        // Створюємо роль, якщо вона не існує
                        \Spatie\Permission\Models\Role::create(['name' => $role]);
                        $user->assignRole($role);
                    }
                } catch (\Exception $e) {
                    \Log::error("Помилка при призначенні ролі: " . $e->getMessage());
                }
            } else {
                // Оновлення Google ID, якщо користувач вже існує, але не має Google ID
                if (empty($user->google_id)) {
                    $user->update([
                        'google_id' => $googleUser->getId(),
                        'avatar' => $user->avatar ?: $googleUser->getAvatar(),
                    ]);
                }

                // Якщо користувач не має ролі, призначаємо її на основі домену
                if (!$user->hasAnyRole(['admin', 'student', 'teacher'])) {
                    $role = Str::endsWith($email, '@student.uzhnu.edu.ua') ? 'student' : 'teacher';

                    try {
                        if (\Spatie\Permission\Models\Role::where('name', $role)->exists()) {
                            $user->assignRole($role);
                        } else {
                            \Log::error("Роль '$role' не існує в базі даних");
                            \Spatie\Permission\Models\Role::create(['name' => $role]);
                            $user->assignRole($role);
                        }

                        // Якщо це студент і не вказаний курс, встановлюємо випадковий
                        if ($role === 'student' && !$user->course_number) {
                            $user->update(['course_number' => rand(1, 4)]);
                        }
                    } catch (\Exception $e) {
                        \Log::error("Помилка при призначенні ролі: " . $e->getMessage());
                    }
                }
            }

            // Авторизація користувача
            Auth::login($user);

            return redirect()->intended(route('dashboard'));

        } catch (\Exception $e) {
            return redirect()->route('login')
                ->withErrors(['email' => 'Помилка авторизації через Google: ' . $e->getMessage()]);
        }
    }

    /**
     * Витягує ім'я, прізвище та по батькові з email адреси або даних Google-акаунта.
     *
     * @param string $email
     * @param array|null $googleUserData
     * @return array
     */
    private function extractNameFromEmail(string $email, ?array $googleUserData = null): array
    {
        $result = [
            'first_name' => '',
            'last_name' => '',
            'middle_name' => null,
        ];

        // Спочатку спробуємо отримати дані з Google-акаунта, якщо вони доступні
        if ($googleUserData && isset($googleUserData['given_name']) && isset($googleUserData['family_name'])) {
            $result['first_name'] = $googleUserData['given_name'];
            $result['last_name'] = $googleUserData['family_name'];

            // Якщо є middle_name в даних Google
            if (isset($googleUserData['middle_name'])) {
                $result['middle_name'] = $googleUserData['middle_name'];
            }

            return $result;
        }

        // Якщо немає даних з Google або вони неповні, використовуємо email
        $username = Str::before($email, '@');
        $parts = explode('.', $username);

        if (count($parts) >= 2) {
            $result['first_name'] = ucfirst($parts[0]);
            $result['last_name'] = ucfirst($parts[1]);

            if (count($parts) >= 3) {
                $result['middle_name'] = ucfirst($parts[2]);
            }
        } else {
            // Якщо формат не відповідає очікуваному, використовуємо весь username як ім'я
            $result['first_name'] = ucfirst($username);
        }

        return $result;
    }
}
