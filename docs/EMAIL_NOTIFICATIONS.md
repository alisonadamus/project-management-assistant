# Система Email Повідомлень

## Огляд

Система автоматично надсилає email повідомлення користувачам про події та підподії в проекті. Повідомлення надсилаються тільки тим користувачам, які мають доступ до конкретної події.

## Типи повідомлень

### 1. Повідомлення про події

#### EventStartNotification
- **Коли**: Надсилається в день початку події
- **Кому**: Студентам відповідного курсу та викладачам-керівникам
- **Зміст**: Інформація про початок події з посиланням для перегляду

#### EventStartingSoonNotification
- **Коли**: Надсилається за 2 дні до початку події
- **Кому**: Студентам відповідного курсу та викладачам-керівникам
- **Зміст**: Нагадування про майбутню подію з посиланням для перегляду

#### EventEndingSoonNotification
- **Коли**: Надсилається за 2 дні до закінчення події
- **Кому**: Студентам відповідного курсу та викладачам-керівникам
- **Зміст**: Нагадування про закінчення події з посиланням для перегляду

### 2. Повідомлення про підподії

#### SubeventStartNotification
- **Коли**: Надсилається в день початку підподії
- **Кому**: Студентам відповідного курсу та викладачам-керівникам основної події
- **Зміст**: Інформація про початок підподії з посиланням на основну подію

#### SubeventStartingSoonNotification
- **Коли**: Надсилається за 1 день до початку підподії
- **Кому**: Студентам відповідного курсу та викладачам-керівникам основної події
- **Зміст**: Нагадування про майбутню підподію з посиланням на основну подію

### 3. Повідомлення про заявки

#### NewOfferNotification
- **Коли**: Надсилається при створенні нової заявки студентом
- **Кому**: Науковому керівнику проекту
- **Зміст**: Інформація про нову заявку з даними студента та посиланням на управління заявками

#### OfferApprovedNotification
- **Коли**: Надсилається при затвердженні заявки викладачем
- **Кому**: Студенту, чию заявку затвердили
- **Зміст**: Підтвердження призначення до проекту з контактами керівника

#### OfferRejectedNotification
- **Коли**: Надсилається при відхиленні заявки (різні причини)
- **Кому**: Студенту, чию заявку відхилили
- **Зміст**: Повідомлення про відхилення з причиною та посиланням на інші проекти

**Причини відхилення:**
- `manual` - Пряме відхилення викладачем
- `other_student_approved` - Призначено іншого студента на проект
- `student_assigned_elsewhere` - Студента призначено до іншого проекту в події
- `supervisor_slots_full` - У керівника закінчилися вільні місця

### 4. Повідомлення про чат

#### NewChatMessageNotification
- **Коли**: Надсилається при отриманні нового повідомлення в чаті проекту
- **Кому**: Іншому учаснику чату (викладач або студент)
- **Зміст**: Інформація про нове повідомлення з превью тексту та посиланням на проект

## Правила доступу

### Студенти
Отримують повідомлення тільки про події, курс категорії яких відповідає їх `course_number`.

### Викладачі
Отримують повідомлення тільки про ті події, де вони є науковими керівниками (записані в таблиці `supervisors`).

### Адміністратори
Не отримують автоматичних повідомлень про події (можна додати за потреби).

## Команди

### app:send-event-start-notifications
```bash
php artisan app:send-event-start-notifications
```
- Надсилає повідомлення про події, які починаються сьогодні
- Надсилає нагадування про події, які починаються через 2 дні
- Надсилає нагадування про події, які закінчуються через 2 дні
- Запускається щодня о 8:00 ранку

### app:send-subevent-notifications
```bash
php artisan app:send-subevent-notifications
```
- Надсилає повідомлення про підподії, які починаються сьогодні
- Надсилає нагадування про підподії, які починаються завтра
- Запускається щодня о 8:30 ранку

## Планувальник завдань

Команди автоматично запускаються через Laravel Scheduler:

```php
// app/Console/Kernel.php
$schedule->command('app:send-event-start-notifications')
    ->dailyAt('08:00')
    ->timezone('Europe/Kiev');

$schedule->command('app:send-subevent-notifications')
    ->dailyAt('08:30')
    ->timezone('Europe/Kiev');
```

Для роботи планувальника потрібно налаштувати cron:
```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

## Конфігурація Email

### Налаштування в .env
```env
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-email@domain.com
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@domain.com
MAIL_FROM_NAME="Project Management Assistant"
```

### Для розробки
```env
MAIL_MAILER=log
```
Повідомлення будуть записуватися в `storage/logs/laravel.log`

## Черги (Queues)

Всі повідомлення реалізують `ShouldQueue`, тому вони будуть оброблятися в фоновому режимі.

Для роботи черг:
```bash
php artisan queue:work
```

## Тестування

### Запуск тестів
```bash
# Тести для подій
php artisan test tests/Feature/SendEventStartNotificationsTest.php

# Тести для підподій
php artisan test tests/Feature/SendSubeventNotificationsTest.php

# Тести для заявок
php artisan test tests/Feature/OfferNotificationsTest.php
```

### Ручне тестування
```bash
# Перевірка команд
php artisan app:send-event-start-notifications
php artisan app:send-subevent-notifications

# Перевірка списку команд
php artisan list | grep send
```

## Логування

Всі помилки надсилання повідомлень логуються в `storage/logs/laravel.log` з контекстом:
- ID події/підподії
- ID користувача
- Текст помилки

## Розширення системи

### Додавання нових типів повідомлень
1. Створити новий Notification клас
2. Додати логіку в відповідну команду
3. Написати тести

### Зміна часу надсилання
Відредагувати `app/Console/Kernel.php` та змінити час у методі `schedule()`.

### Додавання нових каналів
Окрім email, можна додати SMS, push-повідомлення тощо, змінивши метод `via()` в Notification класах.
