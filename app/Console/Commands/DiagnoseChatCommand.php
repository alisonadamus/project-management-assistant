<?php

namespace Alison\ProjectManagementAssistant\Console\Commands;

use Alison\ProjectManagementAssistant\Models\Project;
use Alison\ProjectManagementAssistant\Models\User;
use Illuminate\Console\Command;

class DiagnoseChatCommand extends Command
{
    protected $signature = 'diagnose:chat {projectId}';
    protected $description = 'Діагностика доступу користувачів до чату проекту';

    public function handle()
    {
        $projectId = $this->argument('projectId');
        
        $project = Project::with(['supervisor.user', 'assignedTo'])->find($projectId);
        
        if (!$project) {
            $this->error("Проект з ID {$projectId} не знайдено");
            return 1;
        }

        $this->info("=== Діагностика проекту {$project->name} ===");
        $this->info("ID проекту: {$project->id}");
        $this->info("Призначений студент: " . ($project->assigned_to ?? 'немає'));
        $this->info("Керівник: " . ($project->supervisor ? $project->supervisor->user_id : 'немає'));

        if ($project->supervisor) {
            $supervisor = $project->supervisor->user;
            $this->info("Керівник: {$supervisor->full_name} (ID: {$supervisor->id})");
            $this->info("Ролі керівника: " . $supervisor->roles->pluck('name')->join(', '));
        }

        if ($project->assignedTo) {
            $student = $project->assignedTo;
            $this->info("Студент: {$student->full_name} (ID: {$student->id})");
            $this->info("Ролі студента: " . $student->roles->pluck('name')->join(', '));
        }

        // Тестуємо авторизацію каналу для кожного користувача
        $this->info("\n=== Тестування авторизації каналу ===");
        
        $users = User::all();
        foreach ($users as $user) {
            $hasAccess = $this->testChannelAccess($user, $project);
            $status = $hasAccess ? '✅' : '❌';
            $this->info("{$status} {$user->full_name} (ID: {$user->id}) - Ролі: " . $user->roles->pluck('name')->join(', '));
        }

        return 0;
    }

    private function testChannelAccess(User $user, Project $project): bool
    {
        // Симулюємо логіку з routes/channels.php
        if (!$project) {
            return false;
        }

        // Адміністратор має доступ до всіх проектів
        if ($user->hasRole('admin')) {
            return true;
        }

        // Викладач повинен бути керівником проекту
        if ($user->hasRole('teacher')) {
            return $project->supervisor && $project->supervisor->user_id == $user->id;
        }

        // Студент повинен бути призначений до проекту
        if ($user->hasRole('student')) {
            return $project->assigned_to == $user->id;
        }

        return false;
    }
}
