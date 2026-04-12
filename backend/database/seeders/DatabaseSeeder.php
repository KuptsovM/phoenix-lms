<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Lecture;
use App\Models\LectureMaterial;
use App\Models\Test;
use App\Models\TestQuestion;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // ============================================
        // СОЗДАНИЕ ПРАВ (PERMISSIONS)
        // ============================================
        $permissions = collect([
            'manage users',
            'manage roles',
            'manage permissions',
            'manage courses',
        ])->map(fn (string $name) => Permission::firstOrCreate([
            'name' => $name,
            'guard_name' => 'web',
        ]));

        // ============================================
        // СОЗДАНИЕ РОЛЕЙ
        // ============================================

        // 1. Роль Super Admin (полный доступ)
        $superAdminRole = Role::firstOrCreate([
            'name' => 'super-admin',
            'guard_name' => 'web',
        ]);
        $superAdminRole->syncPermissions($permissions);

        // 2. Роль Admin (полный доступ)
        $adminRole = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'web',
        ]);
        $adminRole->syncPermissions($permissions);

        // 3. Роль Content Manager (управление курсами)
        $contentManagerRole = Role::firstOrCreate([
            'name' => 'content-manager',
            'guard_name' => 'web',
        ]);
        $contentManagerRole->syncPermissions([
            'manage courses',
        ]);

        // 4. Роль User Manager (управление пользователями)
        $userManagerRole = Role::firstOrCreate([
            'name' => 'user-manager',
            'guard_name' => 'web',
        ]);
        $userManagerRole->syncPermissions([
            'manage users',
        ]);

        // 5. Роль Role Manager (управление ролями и правами)
        $roleManagerRole = Role::firstOrCreate([
            'name' => 'role-manager',
            'guard_name' => 'web',
        ]);
        $roleManagerRole->syncPermissions([
            'manage roles',
            'manage permissions',
        ]);

        // 6. Роль Teacher (преподаватель)
        $teacherRole = Role::firstOrCreate([
            'name' => 'teacher',
            'guard_name' => 'web',
        ]);
        $teacherRole->syncPermissions([
            'manage courses',
        ]);

        // 7. Роль Student (студент)
        $studentRole = Role::firstOrCreate([
            'name' => 'student',
            'guard_name' => 'web',
        ]);

        // ============================================
        // СОЗДАНИЕ ТЕСТОВЫХ ПОЛЬЗОВАТЕЛЕЙ
        // ============================================

        // 1. Super Admin (полный доступ)
        $superAdmin = User::query()->updateOrCreate(
            ['email' => 'superadmin@example.com'],
            [
                'name' => 'Иванов Иван Иванович',
                'password' => 'password',
            ],
        );
        $superAdmin->syncRoles([$superAdminRole]);

        // 2. Admin (полный доступ)
        $admin = User::query()->updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Смирнов Алексей Петрович',
                'password' => 'password',
            ],
        );
        $admin->syncRoles([$adminRole]);

        // 3. Content Manager (только курсы)
        $contentManager = User::query()->updateOrCreate(
            ['email' => 'content@example.com'],
            [
                'name' => 'Кузнецова Мария Владимировна',
                'password' => 'password',
            ],
        );
        $contentManager->syncRoles([$contentManagerRole]);

        // 4. User Manager (только пользователи)
        $userManager = User::query()->updateOrCreate(
            ['email' => 'usermanager@example.com'],
            [
                'name' => 'Соколов Дмитрий Андреевич',
                'password' => 'password',
            ],
        );
        $userManager->syncRoles([$userManagerRole]);

        // 5. Role Manager (только роли и права)
        $roleManager = User::query()->updateOrCreate(
            ['email' => 'rolemanager@example.com'],
            [
                'name' => 'Попова Анна Сергеевна',
                'password' => 'password',
            ],
        );
        $roleManager->syncRoles([$roleManagerRole]);

        // 6. Обычный пользователь (без доступа к админке)
        User::query()->updateOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'Лебедев Максим Игоревич',
                'password' => 'password',
            ],
        );

        // 7. Тестовый пользователь (без доступа к админке)
        User::query()->updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Козлов Илья Николаевич',
                'password' => 'password',
            ],
        );

        // 8. Teacher (преподаватель)
        $teacher = User::query()->updateOrCreate(
            ['email' => 'teacher@example.com'],
            [
                'name' => 'Новикова Екатерина Михайловна',
                'password' => 'password',
            ],
        );
        $teacher->syncRoles([$teacherRole]);

        // 9. Student (студент)
        $student = User::query()->updateOrCreate(
            ['email' => 'student@example.com'],
            [
                'name' => 'Морозов Роман Викторович',
                'password' => 'password',
            ],
        );
        $student->syncRoles([$studentRole]);

        // ============================================
        // СОЗДАНИЕ ДЕМОНСТРАЦИОННОГО КУРСА И ТЕСТОВЫХ ДАННЫХ
        // ============================================
        if (Course::query()->count() === 0) {
            $demoCourse = Course::create([
                'title' => 'Основы веб-разработки на Laravel',
                'slug' => 'osnovy-web-razrabotki-na-laravel',
                'description' => 'Полноценный практический курс по созданию современных веб-приложений с использованием фреймворка Laravel. В курсе рассматриваются архитектура MVC, работа с базами данных, маршрутизация и многое другое.',
                'status' => 'published',
                'is_featured' => true,
                'published_at' => now(),
                'author_id' => $teacher->id,
            ]);

            // Лекция 1
            $lecture1 = Lecture::create([
                'title' => 'Введение в архитектуру MVC',
                'description' => 'Изучение паттерна Model-View-Controller и его реализации в Laravel.',
                'content' => "<h2>Что такое MVC?</h2>\n<p>MVC (Model-View-Controller) — это архитектурный паттерн, разделяющий приложение на три основных компонента:</p>\n<ul>\n<li><strong>Модель (Model)</strong>: отвечает за бизнес-логику и работу с данными.</li>\n<li><strong>Представление (View)</strong>: отвечает за отображение данных пользователю.</li>\n<li><strong>Контроллер (Controller)</strong>: связывает модель и представление, обрабатывая пользовательский ввод.</li>\n</ul>\n<p>В Laravel этот паттерн реализован на базовом уровне и является стандартом для разработки.</p>",
                'status' => 'published',
                'course_id' => $demoCourse->id,
                'order' => 1,
            ]);

            LectureMaterial::create([
                'title' => 'Презентация: Паттерн MVC',
                'file_url' => '/materials/mvc_presentation.pdf',
                'file_size' => '2048 KB',
                'file_type' => 'PDF',
                'lecture_id' => $lecture1->id,
            ]);

            // Лекция 2
            $lecture2 = Lecture::create([
                'title' => 'Маршрутизация (Routing)',
                'description' => 'Как работает система маршрутизации в Laravel и как создавать RESTful API.',
                'content' => "<h2>Основы маршрутизации</h2>\n<p>Маршрутизация в Laravel осуществляется в файлах папки <code>routes</code>. Основные веб-маршруты описываются в <code>routes/web.php</code>.</p>\n<p>Пример простейшего маршрута:</p>\n<pre><code>Route::get('/hello', function () {\n    return 'Hello, World!';\n});</code></pre>\n<p>Также Laravel поддерживает параметры в маршрутах, именованные маршруты и группы маршрутов для удобной организации кода.</p>",
                'status' => 'published',
                'course_id' => $demoCourse->id,
                'order' => 2,
            ]);

            LectureMaterial::create([
                'title' => 'Справочник по маршрутам',
                'file_url' => '/materials/routing_cheatsheet.pdf',
                'file_size' => '1024 KB',
                'file_type' => 'PDF',
                'lecture_id' => $lecture2->id,
            ]);

            // Тест для демо-курса
            $test1 = Test::create([
                'title' => 'Итоговый тест по основам Laravel',
                'description' => 'Проверьте свои знания по архитектуре MVC и маршрутизации.',
                'duration' => 30,
                'difficulty' => 'medium',
                'status' => 'published',
                'questions_count' => 3,
                'course_id' => $demoCourse->id,
            ]);

            // Вопрос 1
            TestQuestion::create([
                'question' => 'За что отвечает буква M в аббревиатуре MVC?',
                'type' => 'multiple_choice',
                'options' => json_encode(['Маршрутизация (Middleware)', 'Модель (Model)', 'Модуль (Module)', 'Метод (Method)']),
                'correct_answer' => 'Модель (Model)',
                'points' => 1,
                'test_id' => $test1->id,
            ]);

            // Вопрос 2
            TestQuestion::create([
                'question' => 'В каком файле по умолчанию объявляются веб-маршруты в Laravel?',
                'type' => 'multiple_choice',
                'options' => json_encode(['routes/api.php', 'app/Http/routes.php', 'routes/web.php', 'config/routes.php']),
                'correct_answer' => 'routes/web.php',
                'points' => 1,
                'test_id' => $test1->id,
            ]);

            // Вопрос 3
            TestQuestion::create([
                'question' => 'Паттерн MVC разделяет приложение на Модель, Представление и Контроллер. Правда ли это?',
                'type' => 'boolean',
                'correct_answer' => 'true',
                'points' => 1,
                'test_id' => $test1->id,
            ]);
        }

        // ============================================
        // ВЫВОД ИНФОРМАЦИИ
        // ============================================
        $this->command->info('=== Тестовые данные для системы созданы ===');
        $this->command->info('');
        $this->command->info('Демонстрационный курс: "Основы веб-разработки на Laravel" (с лекциями и тестами)');
        $this->command->info('');
        $this->command->info('Пользователи с доступом к админке:');
        $this->command->info('  - superadmin@example.com / password (Иванов И.И. - полный доступ)');
        $this->command->info('  - admin@example.com / password (Смирнов А.П. - полный доступ)');
        $this->command->info('  - content@example.com / password (Кузнецова М.В. - только курсы)');
        $this->command->info('  - usermanager@example.com / password (Соколов Д.А. - только пользователи)');
        $this->command->info('  - rolemanager@example.com / password (Попова А.С. - только роли и права)');
        $this->command->info('');
        $this->command->info('Пользователи без доступа к админке:');
        $this->command->info('  - user@example.com / password (Лебедев М.И.)');
        $this->command->info('  - test@example.com / password (Козлов И.Н.)');
        $this->command->info('');
        $this->command->info('Пользователи для платформы обучения:');
        $this->command->info('  - teacher@example.com / password (Новикова Е.М. - преподаватель, автор курса)');
        $this->command->info('  - student@example.com / password (Морозов Р.В. - студент)');
        $this->command->info('');
    }

    /**
     * Получить случайный заголовок лекции
     */
    private function getRandomLectureTitle(): string
    {
        $titles = [
            'Введение в предмет',
            'Основные концепции',
            'Практическое применение',
            'Продвинутые техники',
            'Заключение и итоги',
            'История развития',
            'Теоретические основы',
            'Практические примеры',
            'Анализ кейсов',
            'Будущие перспективы',
        ];

        return $titles[array_rand($titles)];
    }

    /**
     * Получить случайное содержимое лекции
     */
    private function getRandomLectureContent(): string
    {
        return "<h2>Обзор темы</h2>
        <p>В этой лекции мы рассмотрим ключевые аспекты изучаемой темы. Материал представлен в доступной форме и включает практические примеры.</p>
        
        <h3>Основные понятия</h3>
        <p>При изучении данной темы важно понимать следующие концепции:</p>
        <ul>
            <li>Определение основных терминов</li>
            <li>Взаимосвязь между концепциями</li>
            <li>Практическое применение</li>
        </ul>
        
        <h3>Примеры из практики</h3>
        <p>Рассмотрим несколько примеров, которые помогут лучше понять материал:</p>
        <p>Пример 1: Описание конкретной ситуации с применением изучаемых концепций.</p>
        <p>Пример 2: Анализ реального кейса из практики.</p>
        
        <h3>Выводы</h3>
        <p>В результате изучения этой лекции вы сможете:</p>
        <ol>
            <li>Понимать основные принципы</li>
            <li>Применять знания на практике</li>
            <li>Анализировать сложные ситуации</li>
        </ol>";
    }

    /**
     * Получить правильный ответ в зависимости от типа вопроса
     */
    private function getCorrectAnswer(string $type): string
    {
        switch ($type) {
            case 'multiple_choice':
                return 'Вариант А';
            case 'boolean':
                return 'true';
            case 'text':
                return 'Правильный ответ на вопрос';
            default:
                return '';
        }
    }
}
