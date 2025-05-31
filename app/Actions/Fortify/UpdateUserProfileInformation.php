<?php

namespace Alison\ProjectManagementAssistant\Actions\Fortify;

use Alison\ProjectManagementAssistant\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;

class UpdateUserProfileInformation implements UpdatesUserProfileInformation
{
    /**
     * Validate and update the given user's profile information.
     *
     * @param array<string, mixed> $input
     * @throws ValidationException
     */
    public function update(User $user, array $input): void
    {
        // Визначаємо правила валідації для номера курсу в залежності від ролі
        $courseRules = ['nullable', 'integer', 'min:1', 'max:4'];
        if ($user->hasRole('student')) {
            $courseRules = ['required', 'integer', 'min:1', 'max:4'];
        }

        Validator::make($input, [
            'name' => ['required', 'string', 'max:32', Rule::unique('users')->ignore($user->id)],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'photo' => ['nullable', 'mimes:jpg,jpeg,png', 'max:1024'],
            'first_name' => ['required', 'string', 'max:50'],
            'last_name' => ['required', 'string', 'max:50'],
            'middle_name' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:512'],
            'course_number' => $courseRules,
        ])->validateWithBag('updateProfileInformation');

        if (isset($input['photo'])) {
            $user->updateProfilePhoto($input['photo']);
        }

        if ($input['email'] !== $user->email &&
            $user instanceof MustVerifyEmail) {
            $this->updateVerifiedUser($user, $input);
        } else {
            $user->forceFill([
                'name' => $input['name'],
                'email' => $input['email'],
                'first_name' => $input['first_name'],
                'last_name' => $input['last_name'],
                'middle_name' => $input['middle_name'] ?? null,
                'description' => $input['description'] ?? null,
                'course_number' => $input['course_number'] ?? null,
            ])->save();
        }
    }

    /**
     * Update the given verified user's profile information.
     *
     * @param array<string, string> $input
     */
    protected function updateVerifiedUser(User $user, array $input): void
    {
        $user->forceFill([
            'name' => $input['name'],
            'email' => $input['email'],
            'email_verified_at' => null,
            'first_name' => $input['first_name'],
            'last_name' => $input['last_name'],
            'middle_name' => $input['middle_name'] ?? null,
            'description' => $input['description'] ?? null,
            'course_number' => $input['course_number'] ?? null,
        ])->save();

        $user->sendEmailVerificationNotification();
    }
}
