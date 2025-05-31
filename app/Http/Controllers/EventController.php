<?php

namespace Alison\ProjectManagementAssistant\Http\Controllers;

use Alison\ProjectManagementAssistant\Models\Event;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    /**
     * Відображення головної сторінки з актуальними подіями
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $page = $request->get('page', 1);
        $cacheKey = "events_index_" . md5(json_encode($request->query()) . "_page_{$page}_user_{$user->id}");
        $cacheDuration = now()->addMinutes(30); // Кешуємо на 30 хвилин

        $events = Cache::remember($cacheKey, $cacheDuration, function () use ($user, $request) {
            $query = Event::query()->with(['category', 'projects', 'supervisors.user']);

            // Обмеження доступу до подій
            if ($user->hasRole('teacher')) {
                // Викладачі бачать тільки ті події, де вони є науковими керівниками
                $query->whereHas('supervisors', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                });
            } elseif ($user->hasRole('student')) {
                // Студенти бачать тільки ті події, де курс категорії відповідає їхньому курсу
                $query->whereHas('category', function ($q) use ($user) {
                    $q->where('course_number', $user->course_number);
                });
            }
            // Адміністратори бачать всі події

            // Фільтрація за пошуком
            if ($request->filled('search')) {
                $query->where('name', 'like', '%' . $request->search . '%');
            }

            // Фільтрація за категорією
            if ($request->filled('category')) {
                $query->where('category_id', $request->category);
            }

            // Фільтрація за датою початку
            if ($request->filled('date_from')) {
                $query->whereDate('start_date', '>=', $request->date_from);
            }

            // Фільтрація за датою завершення
            if ($request->filled('date_to')) {
                $query->whereDate('end_date', '<=', $request->date_to);
            }

            return $query->orderBy('start_date')->paginate(9);
        });

        // Додаємо параметри пошуку до пагінації
        $events->appends(request()->query());

        return view('events.index', compact('events'));
    }

    /**
     * Відображення теперішніх подій
     */
    public function current(Request $request): View
    {
        $user = Auth::user();
        $page = $request->get('page', 1);
        $cacheKey = "events_current_" . md5(json_encode($request->query()) . "_page_{$page}_user_{$user->id}");
        $cacheDuration = now()->addMinutes(15); // Кешуємо на 15 хвилин (частіше оновлюється через поточний статус)

        $events = Cache::remember($cacheKey, $cacheDuration, function () use ($user, $request) {
            $query = Event::query()
                ->where('start_date', '<=', Carbon::now())
                ->where('end_date', '>=', Carbon::now())
                ->with(['category', 'projects', 'supervisors.user']);

            // Обмеження доступу до подій
            if ($user->hasRole('teacher')) {
                // Викладачі бачать тільки ті події, де вони є науковими керівниками
                $query->whereHas('supervisors', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                });
            } elseif ($user->hasRole('student')) {
                // Студенти бачать тільки ті події, де курс категорії відповідає їхньому курсу
                $query->whereHas('category', function ($q) use ($user) {
                    $q->where('course_number', $user->course_number);
                });
            }
            // Адміністратори бачать всі події

            // Фільтрація за пошуком
            if ($request->filled('search')) {
                $query->where('name', 'like', '%' . $request->search . '%');
            }

            // Фільтрація за категорією
            if ($request->filled('category')) {
                $query->where('category_id', $request->category);
            }

            return $query->orderBy('start_date')->paginate(9);
        });

        // Додаємо параметри пошуку до пагінації
        $events->appends(request()->query());

        return view('events.current', compact('events'));
    }

    /**
     * Відображення запланованих подій
     */
    public function upcoming(Request $request): View
    {
        $user = Auth::user();
        $page = $request->get('page', 1);
        $cacheKey = "events_upcoming_" . md5(json_encode($request->query()) . "_page_{$page}_user_{$user->id}");
        $cacheDuration = now()->addHour(); // Кешуємо на годину

        $events = Cache::remember($cacheKey, $cacheDuration, function () use ($user, $request) {
            $query = Event::query()
                ->where('start_date', '>', Carbon::now())
                ->with(['category', 'projects', 'supervisors.user']);

            // Обмеження доступу до подій
            if ($user->hasRole('teacher')) {
                // Викладачі бачать тільки ті події, де вони є науковими керівниками
                $query->whereHas('supervisors', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                });
            } elseif ($user->hasRole('student')) {
                // Студенти бачать тільки ті події, де курс категорії відповідає їхньому курсу
                $query->whereHas('category', function ($q) use ($user) {
                    $q->where('course_number', $user->course_number);
                });
            }
            // Адміністратори бачать всі події

            // Фільтрація за пошуком
            if ($request->filled('search')) {
                $query->where('name', 'like', '%' . $request->search . '%');
            }

            // Фільтрація за категорією
            if ($request->filled('category')) {
                $query->where('category_id', $request->category);
            }

            return $query->orderBy('start_date')->paginate(9);
        });

        // Додаємо параметри пошуку до пагінації
        $events->appends(request()->query());

        return view('events.upcoming', compact('events'));
    }

    /**
     * Відображення архівованих подій
     */
    public function archived(Request $request): View
    {
        $user = Auth::user();
        $page = $request->get('page', 1);
        $cacheKey = "events_archived_" . md5(json_encode($request->query()) . "_page_{$page}_user_{$user->id}");
        $cacheDuration = now()->addDay(); // Кешуємо на добу (архівні події рідко змінюються)

        $events = Cache::remember($cacheKey, $cacheDuration, function () use ($user, $request) {
            $query = Event::query()
                ->where('end_date', '<', Carbon::now())
                ->with(['category', 'projects', 'supervisors.user']);

            // Обмеження доступу до подій
            if ($user->hasRole('teacher')) {
                // Викладачі бачать тільки ті події, де вони є науковими керівниками
                $query->whereHas('supervisors', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                });
            } elseif ($user->hasRole('student')) {
                // Студенти бачать тільки ті події, де курс категорії відповідає їхньому курсу
                $query->whereHas('category', function ($q) use ($user) {
                    $q->where('course_number', $user->course_number);
                });
            }
            // Адміністратори бачать всі події

            // Фільтрація за пошуком
            if ($request->filled('search')) {
                $query->where('name', 'like', '%' . $request->search . '%');
            }

            // Фільтрація за категорією
            if ($request->filled('category')) {
                $query->where('category_id', $request->category);
            }

            return $query->orderByDesc('end_date')->paginate(9);
        });

        // Додаємо параметри пошуку до пагінації
        $events->appends(request()->query());

        return view('events.archived', compact('events'));
    }

    /**
     * Відображення деталей події
     */
    public function show(Event $event): View
    {
        \Log::info('Event show method called', ['event_id' => $event->id]);
        
        try {
            // Завантажуємо всі необхідні зв'язки
            $event->load([
                'category', 
                'supervisors.user', 
                'projects.technologies',
                'projects.assignedTo'
            ]);
            
            \Log::info('Event loaded successfully', ['event_id' => $event->id]);
            
            \Log::info('Event loaded', ['event' => $event->toArray()]);
            
            $user = Auth::user();
            if (!$user) {
                \Log::error('User not authenticated');
                abort(403, 'Необхідно увійти в систему');
            }
            
            \Log::info('Authenticated user', ['user_id' => $user->id, 'roles' => $user->getRoleNames()]);
            
            $cacheKey = "event_{$event->id}_show";
            $cacheDuration = now()->addHours(6); // Кешуємо на 6 годин

            $event = Cache::remember($cacheKey, $cacheDuration, function () use ($event) {
                return $event->load([
                    'category', 
                    'supervisors.user', 
                    'projects.technologies',
                    'projects.assignedTo'
                ]);
            });

            // Перевірка доступу до події
            if ($user->hasRole('admin')) {
                // Адміністратори мають доступ до всіх подій
                \Log::info('Admin access granted', ['user_id' => $user->id]);
            } elseif ($user->hasRole('teacher')) {
                $isSupervisor = $event->supervisors->contains('user_id', $user->id);
                if (!$isSupervisor) {
                    \Log::warning('Teacher is not a supervisor', [
                        'user_id' => $user->id, 
                        'event_id' => $event->id
                    ]);
                    abort(403, 'Ви не маєте доступу до цієї події, оскільки не є її науковим керівником');
                }
            } elseif ($user->hasRole('student')) {
                if ($event->category->course_number != $user->course_number) {
                    \Log::warning('Student course mismatch', [
                        'user_course' => $user->course_number, 
                        'event_course' => $event->category->course_number
                    ]);
                    abort(403, 'Ви не маєте доступу до цієї події, оскільки вона призначена для іншого курсу');
                }
            } else {
                abort(403, 'У вас немає прав для перегляду цієї події');
            }

            return view('events.show', compact('event'));
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            \Log::error('Event not found', ['event_id' => $id, 'error' => $e->getMessage()]);
            abort(404, 'Подію не знайдено');
        } catch (\Exception $e) {
            \Log::error('Error showing event', ['event_id' => $id, 'error' => $e->getMessage()]);
            abort(500, 'Сталася помилка при завантаженні події');
        }
    }
}
