<?php

namespace Alison\ProjectManagementAssistant\Actions\Fortify;

use Alison\ProjectManagementAssistant\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Витягує ім'я, прізвище та по батькові з email адреси.
     *
     * @param string $email
     * @return array
     */
    private function extractNameFromEmail(string $email): array
    {
        $result = [
            'first_name' => '',
            'last_name' => '',
            'middle_name' => null,
        ];

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

    /**
     * Validate and create a newly registered user.
     *
     * @param array<string, string> $input
     * @throws ValidationException
     */
    public function create(array $input): User
    {
        // Перевірка домену електронної пошти
        $email = $input['email'];
        $allowedDomains = ['student.uzhnu.edu.ua', 'uzhnu.edu.ua'];
        $isAllowedDomain = false;

        foreach ($allowedDomains as $domain) {
            if (Str::endsWith($email, '@' . $domain)) {
                $isAllowedDomain = true;
                break;
            }
        }

        if (!$isAllowedDomain) {
            throw ValidationException::withMessages([
                'email' => __('Дозволена реєстрація лише з доменами @student.uzhnu.edu.ua або @uzhnu.edu.ua'),
            ]);
        }

        // Визначення ролі на основі домену
        $isStudent = Str::endsWith($email, '@student.uzhnu.edu.ua');

        // Додаткова валідація для студентів (обов'язковий курс)
        $courseRules = ['nullable', 'integer', 'min:1', 'max:4'];
        if ($isStudent) {
            $courseRules = ['required', 'integer', 'min:1', 'max:4'];
        }

        // Отримуємо дані з email
        $nameParts = $this->extractNameFromEmail($email);

        // Якщо поля не заповнені користувачем, використовуємо дані з email
        if (empty($input['first_name'])) {
            $input['first_name'] = $nameParts['first_name'];
        }

        if (empty($input['last_name'])) {
            $input['last_name'] = $nameParts['last_name'];
        }

        if (empty($input['middle_name']) && $nameParts['middle_name'] !== null) {
            $input['middle_name'] = $nameParts['middle_name'];
        }

        // Валідація основних полів
        Validator::make($input, [
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => $this->passwordRules(),
            'first_name' => ['nullable', 'string', 'max:50'], // Змінюємо на nullable, бо будемо автоматично заповнювати
            'last_name' => ['nullable', 'string', 'max:50'], // Змінюємо на nullable
            'middle_name' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:512'],
            'course_number' => $courseRules,
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ])->validate();

        // Створення користувача
        $user = User::query()->create([
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
            'first_name' => $input['first_name'],
            'last_name' => $input['last_name'],
            'middle_name' => $input['middle_name'] ?? null,
            'description' => $input['description'] ?? null,
            'course_number' => $isStudent ? $input['course_number'] : null,
            'email_verified_at' => now(), // Автоматична верифікація, оскільки домен перевірений
        ]);

        // Призначення ролі
        $role = $isStudent ? 'student' : 'teacher';
        $user->assignRole($role);

        return $user;
    }
}
