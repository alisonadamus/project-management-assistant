<?php

namespace Tests\Feature;

use Alison\ProjectManagementAssistant\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Jetstream\Http\Livewire\UpdateProfileInformationForm;
use Livewire\Livewire;
use Illuminate\Foundation\Testing\TestCase;

class ProfileInformationTest extends TestCase
{
    use RefreshDatabase;

    public function test_current_profile_information_is_available(): void
    {
        $this->actingAs($user = User::factory()->create());

        $component = Livewire::test(UpdateProfileInformationForm::class);

        $this->assertEquals($user->name, $component->state['name']);
        $this->assertEquals($user->email, $component->state['email']);
        $this->assertEquals($user->first_name, $component->state['first_name']);
        $this->assertEquals($user->last_name, $component->state['last_name']);
        $this->assertEquals($user->middle_name, $component->state['middle_name']);
        $this->assertEquals($user->description, $component->state['description']);
        $this->assertEquals($user->course_number, $component->state['course_number']);
    }

    public function test_profile_information_can_be_updated(): void
    {
        $this->actingAs($user = User::factory()->create());

        Livewire::test(UpdateProfileInformationForm::class)
            ->set('state', [
                'name' => 'testuser',
                'email' => 'test@example.com',
                'first_name' => 'Test',
                'last_name' => 'User',
                'middle_name' => 'Middle',
                'description' => 'Test description',
                'course_number' => 3,
            ])
            ->call('updateProfileInformation');

        $fresh = $user->fresh();
        $this->assertEquals('testuser', $fresh->name);
        $this->assertEquals('test@example.com', $fresh->email);
        $this->assertEquals('Test', $fresh->first_name);
        $this->assertEquals('User', $fresh->last_name);
        $this->assertEquals('Middle', $fresh->middle_name);
        $this->assertEquals('Test description', $fresh->description);
        $this->assertEquals(3, $fresh->course_number);
    }
}
