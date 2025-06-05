<?php

namespace Tests\Feature;

use Alison\ProjectManagementAssistant\Models\Project;
use Alison\ProjectManagementAssistant\Models\Event;
use Alison\ProjectManagementAssistant\Models\Technology;
use Alison\ProjectManagementAssistant\Models\User;
use Alison\ProjectManagementAssistant\Models\Supervisor;
use Alison\ProjectManagementAssistant\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;

class ProjectFilterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Створюємо ролі
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'teacher']);
        Role::create(['name' => 'student']);
    }

    /** @test */
    public function it_can_filter_projects_by_search()
    {
        $user = User::factory()->create();
        $user->assignRole('admin');
        
        $project1 = Project::factory()->create(['name' => 'Laravel Project']);
        $project2 = Project::factory()->create(['name' => 'Vue.js Application']);
        $project3 = Project::factory()->create(['body' => 'This project uses Laravel framework']);

        $response = $this->actingAs($user)->get(route('projects.index', ['search' => 'Laravel']));

        $response->assertStatus(200);
        $response->assertSee('Laravel Project');
        $response->assertSee('This project uses Laravel framework');
        $response->assertDontSee('Vue.js Application');
    }

    /** @test */
    public function it_can_filter_projects_by_event()
    {
        $user = User::factory()->create();
        $user->assignRole('admin');
        
        $category = Category::factory()->create();
        $event1 = Event::factory()->create(['category_id' => $category->id]);
        $event2 = Event::factory()->create(['category_id' => $category->id]);
        
        $project1 = Project::factory()->create(['event_id' => $event1->id]);
        $project2 = Project::factory()->create(['event_id' => $event2->id]);

        $response = $this->actingAs($user)->get(route('projects.index', ['event' => $event1->id]));

        $response->assertStatus(200);
        $response->assertSee($project1->name);
        $response->assertDontSee($project2->name);
    }

    /** @test */
    public function it_can_filter_projects_by_technology()
    {
        $user = User::factory()->create();
        $user->assignRole('admin');
        
        $technology1 = Technology::factory()->create(['name' => 'Laravel']);
        $technology2 = Technology::factory()->create(['name' => 'Vue.js']);
        
        $project1 = Project::factory()->create();
        $project2 = Project::factory()->create();
        
        $project1->technologies()->attach($technology1);
        $project2->technologies()->attach($technology2);

        $response = $this->actingAs($user)->get(route('projects.index', ['technology' => $technology1->id]));

        $response->assertStatus(200);
        $response->assertSee($project1->name);
        $response->assertDontSee($project2->name);
    }

    /** @test */
    public function it_can_filter_projects_by_status()
    {
        $user = User::factory()->create();
        $user->assignRole('admin');
        
        $student = User::factory()->create();
        
        $project1 = Project::factory()->create(['assigned_to' => $student->id]);
        $project2 = Project::factory()->create(['assigned_to' => null]);

        // Тест фільтру "призначені"
        $response = $this->actingAs($user)->get(route('projects.index', ['status' => 'assigned']));
        $response->assertStatus(200);
        $response->assertSee($project1->name);
        $response->assertDontSee($project2->name);

        // Тест фільтру "не призначені"
        $response = $this->actingAs($user)->get(route('projects.index', ['status' => 'unassigned']));
        $response->assertStatus(200);
        $response->assertSee($project2->name);
        $response->assertDontSee($project1->name);
    }

    /** @test */
    public function it_can_sort_projects_by_name()
    {
        $user = User::factory()->create();
        $user->assignRole('admin');
        
        $project1 = Project::factory()->create(['name' => 'B Project']);
        $project2 = Project::factory()->create(['name' => 'A Project']);

        $response = $this->actingAs($user)->get(route('projects.index', ['sort_by' => 'name', 'sort_direction' => 'asc']));

        $response->assertStatus(200);
        $content = $response->getContent();
        $posA = strpos($content, 'A Project');
        $posB = strpos($content, 'B Project');
        $this->assertLessThan($posB, $posA);
    }

    /** @test */
    public function teacher_sees_only_their_supervised_projects()
    {
        $teacher = User::factory()->create();
        $teacher->assignRole('teacher');
        
        $otherTeacher = User::factory()->create();
        $otherTeacher->assignRole('teacher');
        
        $category = Category::factory()->create();
        $event = Event::factory()->create(['category_id' => $category->id]);
        
        $supervisor1 = Supervisor::factory()->create(['user_id' => $teacher->id, 'event_id' => $event->id]);
        $supervisor2 = Supervisor::factory()->create(['user_id' => $otherTeacher->id, 'event_id' => $event->id]);
        
        $project1 = Project::factory()->create(['supervisor_id' => $supervisor1->id]);
        $project2 = Project::factory()->create(['supervisor_id' => $supervisor2->id]);

        $response = $this->actingAs($teacher)->get(route('projects.index'));

        $response->assertStatus(200);
        $response->assertSee($project1->name);
        $response->assertDontSee($project2->name);
    }

    /** @test */
    public function student_sees_only_assigned_projects()
    {
        $student = User::factory()->create();
        $student->assignRole('student');
        
        $otherStudent = User::factory()->create();
        
        $project1 = Project::factory()->create(['assigned_to' => $student->id]);
        $project2 = Project::factory()->create(['assigned_to' => $otherStudent->id]);
        $project3 = Project::factory()->create(['assigned_to' => null]);

        $response = $this->actingAs($student)->get(route('projects.index'));

        $response->assertStatus(200);
        $response->assertSee($project1->name);
        $response->assertDontSee($project2->name);
        $response->assertDontSee($project3->name);
    }

    /** @test */
    public function admin_sees_all_projects()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        
        $project1 = Project::factory()->create();
        $project2 = Project::factory()->create();
        $project3 = Project::factory()->create();

        $response = $this->actingAs($admin)->get(route('projects.index'));

        $response->assertStatus(200);
        $response->assertSee($project1->name);
        $response->assertSee($project2->name);
        $response->assertSee($project3->name);
    }

    /** @test */
    public function it_shows_filter_form_elements()
    {
        $user = User::factory()->create();
        $user->assignRole('admin');
        
        $event = Event::factory()->create(['category_id' => Category::factory()->create()->id]);
        $technology = Technology::factory()->create();

        $response = $this->actingAs($user)->get(route('projects.index'));

        $response->assertStatus(200);
        $response->assertSee('Пошук за назвою або описом');
        $response->assertSee('Подія');
        $response->assertSee('Технологія');
        $response->assertSee('Статус');
        $response->assertSee('Фільтрувати');
        $response->assertSee('Сортувати за:');
        $response->assertSee($event->name);
        $response->assertSee($technology->name);
    }
}
