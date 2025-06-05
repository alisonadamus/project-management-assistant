<?php

use Alison\ProjectManagementAssistant\Http\Controllers\Auth\GoogleAuthController;
use Alison\ProjectManagementAssistant\Http\Controllers\CategoryController;
use Alison\ProjectManagementAssistant\Http\Controllers\DashboardController;
use Alison\ProjectManagementAssistant\Http\Controllers\EventController;
use Alison\ProjectManagementAssistant\Http\Controllers\MessageController;
use Alison\ProjectManagementAssistant\Http\Controllers\ProjectController;
use Alison\ProjectManagementAssistant\Http\Controllers\PushSubscriptionController;
use Alison\ProjectManagementAssistant\Http\Controllers\StudentOfferController;
use Alison\ProjectManagementAssistant\Http\Controllers\SubeventController;
use Alison\ProjectManagementAssistant\Http\Controllers\SubjectController;
use Alison\ProjectManagementAssistant\Http\Controllers\TeacherCategoryController;
use Alison\ProjectManagementAssistant\Http\Controllers\TeacherEventController;
use Alison\ProjectManagementAssistant\Http\Controllers\TeacherOfferController;
use Alison\ProjectManagementAssistant\Http\Controllers\TeacherProjectController;
use Alison\ProjectManagementAssistant\Http\Controllers\TeacherSubjectController;


use Alison\ProjectManagementAssistant\Http\Controllers\TeacherTechnologyController;
use Alison\ProjectManagementAssistant\Http\Controllers\TechnologyController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Google OAuth маршрути
Route::get('/auth/google/redirect', [GoogleAuthController::class, 'redirectToGoogle'])->name('google.redirect');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'handleGoogleCallback'])->name('google.callback');

// Додаткові маршрути для сумісності з налаштуваннями Google OAuth
Route::get('/login/google/redirect', [GoogleAuthController::class, 'redirectToGoogle']);
Route::get('/login/google/callback', [GoogleAuthController::class, 'handleGoogleCallback']);

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Маршрути для подій (доступні всім авторизованим користувачам)
    Route::get('/events', [EventController::class, 'index'])->middleware('permission:view events')->name('events.index');
    Route::get('/events/current', [EventController::class, 'current'])->middleware('permission:view events')->name('events.current');
    Route::get('/events/upcoming', [EventController::class, 'upcoming'])->middleware('permission:view events')->name('events.upcoming');
    Route::get('/events/archived', [EventController::class, 'archived'])->middleware('permission:view events')->name('events.archived');
    Route::get('/events/{event}', [EventController::class, 'show'])->middleware('permission:view events')->name('events.show');

    // Маршрути для категорій (доступні всім авторизованим користувачам)
    Route::get('/categories', [CategoryController::class, 'index'])->middleware('permission:view categories')->name('categories.index');
    Route::get('/categories/{category}', [CategoryController::class, 'show'])->middleware('permission:view categories')->name('categories.show');

    // Маршрути для предметів (доступні всім авторизованим користувачам)
    Route::get('/subjects', [SubjectController::class, 'index'])->middleware('permission:view subjects')->name('subjects.index');
    Route::get('/subjects/{subject}', [SubjectController::class, 'show'])->middleware('permission:view subjects')->name('subjects.show');

    // Маршрути для технологій (доступні всім авторизованим користувачам)
    Route::get('/technologies', [TechnologyController::class, 'index'])->middleware('permission:view technologies')->name('technologies.index');
    Route::get('/technologies/{technology}', [TechnologyController::class, 'show'])->middleware('permission:view technologies')->name('technologies.show');

    // Маршрути для проектів (доступні всім авторизованим користувачам)
    Route::get('/projects', [ProjectController::class, 'index'])->middleware('permission:view projects')->name('projects.index');
    Route::get('/projects/{project}', [ProjectController::class, 'show'])->middleware('permission:view projects')->name('projects.show');

    // Маршрути для заявок студентів
    Route::get('/offers', [StudentOfferController::class, 'index'])->middleware('permission:view offers')->name('offers.index');
    Route::post('/projects/{project}/offers', [StudentOfferController::class, 'store'])->middleware('permission:create offers')->name('offers.store');
    Route::delete('/projects/{project}/offers', [StudentOfferController::class, 'destroy'])->middleware('permission:delete offers')->name('offers.destroy');

    // Маршрути для чату проекту
    Route::get('/projects/{project}/messages', [MessageController::class, 'getMessages'])->middleware('auth')->name('messages.get');
    Route::post('/projects/{project}/messages', [MessageController::class, 'sendMessage'])->middleware('auth')->name('messages.send');
    Route::post('/projects/{project}/messages/read', [MessageController::class, 'markAsRead'])->middleware('auth')->name('messages.read');

    // Маршрути для push підписок
    Route::post('/push-subscriptions', [PushSubscriptionController::class, 'store'])->name('push-subscriptions.store');
    Route::delete('/push-subscriptions', [PushSubscriptionController::class, 'destroy'])->name('push-subscriptions.destroy');
    Route::get('/push-subscriptions/status', [PushSubscriptionController::class, 'status'])->name('push-subscriptions.status');

    // Тестовий маршрут для push-повідомлень
    Route::get('/test-push-notification', function() {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'Потрібна авторизація'], 401);
        }

        if (!$user->pushSubscriptions()->exists()) {
            return response()->json(['error' => 'Немає push підписок. Увімкніть push в профілі.'], 400);
        }

        // Створюємо тестове повідомлення
        $project = \Alison\ProjectManagementAssistant\Models\Project::whereNotNull('assigned_to')->first();
        if (!$project) {
            return response()->json(['error' => 'Немає проектів для тестування'], 400);
        }

        $message = \Alison\ProjectManagementAssistant\Models\Message::create([
            'project_id' => $project->id,
            'sender_id' => $user->id,
            'message' => 'Тестове повідомлення для push-сповіщення',
            'is_read' => false,
        ]);

        $message->load('sender');

        try {
            $user->notify(new \Alison\ProjectManagementAssistant\Notifications\NewChatMessageNotification($message));
            return response()->json(['success' => 'Push-повідомлення відправлено!']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Помилка: ' . $e->getMessage()], 500);
        }
    })->name('test.push');

    // Маршрути для підподій (доступні всім авторизованим користувачам для перегляду)
    Route::get('/events/{event}/gantt-data', [SubeventController::class, 'getGanttData'])->middleware('auth')->name('subevents.gantt-data');



    // Маршрути для керування подіями (доступні користувачам з відповідними дозволами)
    Route::prefix('teacher')->name('teacher.')->group(function () {
        // Створення, редагування та видалення подій
        Route::get('/events/create', [TeacherEventController::class, 'create'])->middleware('permission:create events')->name('events.create');
        Route::post('/events', [TeacherEventController::class, 'store'])->middleware('permission:create events')->name('events.store');
        Route::get('/events/{event}/edit', [TeacherEventController::class, 'edit'])->middleware('permission:edit events')->name('events.edit');
        Route::put('/events/{event}', [TeacherEventController::class, 'update'])->middleware('permission:edit events')->name('events.update');
        Route::delete('/events/{event}', [TeacherEventController::class, 'destroy'])->middleware('permission:delete events')->name('events.destroy');

        // Управління керівниками подій
        Route::get('/events/{event}/supervisors', [TeacherEventController::class, 'manageSupervisors'])->middleware('permission:view supervisors')->name('events.supervisors');
        Route::post('/events/{event}/supervisors', [TeacherEventController::class, 'addSupervisor'])->middleware('permission:create supervisors')->name('events.supervisors.add');
        Route::put('/events/{event}/supervisors/{supervisor}', [TeacherEventController::class, 'updateSupervisor'])->middleware('permission:edit supervisors')->name('events.supervisors.update');
        Route::delete('/events/{event}/supervisors/{supervisor}', [TeacherEventController::class, 'removeSupervisor'])->middleware('permission:delete supervisors')->name('events.supervisors.remove');

        // Створення, редагування та видалення категорій
        Route::get('/categories/create', [TeacherCategoryController::class, 'create'])->middleware('permission:create categories')->name('categories.create');
        Route::post('/categories', [TeacherCategoryController::class, 'store'])->middleware('permission:create categories')->name('categories.store');
        Route::get('/categories/{category}/edit', [TeacherCategoryController::class, 'edit'])->middleware('permission:edit categories')->name('categories.edit');
        Route::put('/categories/{category}', [TeacherCategoryController::class, 'update'])->middleware('permission:edit categories')->name('categories.update');
        Route::delete('/categories/{category}', [TeacherCategoryController::class, 'destroy'])->middleware('permission:delete categories')->name('categories.destroy');

        // Створення, редагування та видалення предметів
        Route::get('/subjects/create', [TeacherSubjectController::class, 'create'])->middleware('permission:create subjects')->name('subjects.create');
        Route::post('/subjects', [TeacherSubjectController::class, 'store'])->middleware('permission:create subjects')->name('subjects.store');
        Route::get('/subjects/{subject}/edit', [TeacherSubjectController::class, 'edit'])->middleware('permission:edit subjects')->name('subjects.edit');
        Route::put('/subjects/{subject}', [TeacherSubjectController::class, 'update'])->middleware('permission:edit subjects')->name('subjects.update');
        Route::delete('/subjects/{subject}', [TeacherSubjectController::class, 'destroy'])->middleware('permission:delete subjects')->name('subjects.destroy');

        // Створення, редагування та видалення технологій
        Route::get('/technologies/create', [TeacherTechnologyController::class, 'create'])->middleware('permission:create technologies')->name('technologies.create');
        Route::post('/technologies', [TeacherTechnologyController::class, 'store'])->middleware('permission:create technologies')->name('technologies.store');
        Route::get('/technologies/{technology}/edit', [TeacherTechnologyController::class, 'edit'])->middleware('permission:edit technologies')->name('technologies.edit');
        Route::put('/technologies/{technology}', [TeacherTechnologyController::class, 'update'])->middleware('permission:edit technologies')->name('technologies.update');
        Route::delete('/technologies/{technology}', [TeacherTechnologyController::class, 'destroy'])->middleware('permission:delete technologies')->name('technologies.destroy');

        // Створення, редагування та видалення проектів
        Route::get('/events/{event}/projects/create', [TeacherProjectController::class, 'create'])->middleware('permission:create projects')->name('projects.create');
        Route::post('/events/{event}/projects', [TeacherProjectController::class, 'store'])->middleware('permission:create projects')->name('projects.store');
        Route::get('/projects/{project}/edit', [TeacherProjectController::class, 'edit'])->middleware('permission:edit projects')->name('projects.edit');
        Route::put('/projects/{project}', [TeacherProjectController::class, 'update'])->middleware('permission:edit projects')->name('projects.update');
        Route::delete('/projects/{project}', [TeacherProjectController::class, 'destroy'])->middleware('permission:delete projects')->name('projects.destroy');

        // Керування заявками
        Route::get('/offers', [TeacherOfferController::class, 'index'])->middleware('permission:view offers')->name('offers.index');
        Route::get('/projects/{project}/offers', [TeacherOfferController::class, 'showProjectOffers'])->middleware('permission:view offers')->name('offers.project');
        Route::post('/projects/{project}/offers/{studentId}/approve', [TeacherOfferController::class, 'approve'])->name('offers.approve');
        Route::post('/projects/{project}/offers/assign-random', [TeacherOfferController::class, 'assignRandom'])->name('offers.assign-random');
        Route::delete('/projects/{project}/offers/{studentId}/reject', [TeacherOfferController::class, 'reject'])->name('offers.reject');

        // Керування підподіями (доступно тільки науковим керівникам події)
        Route::get('/events/{event}/subevents/create', [SubeventController::class, 'create'])->name('subevents.create');
        Route::post('/events/{event}/subevents', [SubeventController::class, 'store'])->name('subevents.store');
        Route::get('/events/{event}/subevents/{subevent}/edit', [SubeventController::class, 'edit'])->name('subevents.edit');
        Route::put('/events/{event}/subevents/{subevent}', [SubeventController::class, 'update'])->name('subevents.update');
        Route::delete('/events/{event}/subevents/{subevent}', [SubeventController::class, 'destroy'])->name('subevents.destroy');
    });
});
