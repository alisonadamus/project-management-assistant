<?php

use Alison\ProjectManagementAssistant\Models\Project;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('project.{projectId}', function ($user, $projectId) {
    $project = Project::find($projectId);

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
});
