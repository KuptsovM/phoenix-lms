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
                'name' => 'Super Admin',
                'password' => 'password',
            ],
        );
        $superAdmin->syncRoles([$superAdminRole]);

        // 2. Admin (полный доступ)
        $admin = User::query()->updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'password' => 'password',
            ],
        );
        $admin->syncRoles([$adminRole]);

        // 3. Content Manager (только курсы)
        $contentManager = User::query()->updateOrCreate(
            ['email' => 'content@example.com'],
            [
                'name' => 'Content Manager',
                'password' => 'password',
            ],
        );
        $contentManager->syncRoles([$contentManagerRole]);

        // 4. User Manager (только пользователи)
        $userManager = User::query()->updateOrCreate(
            ['email' => 'usermanager@example.com'],
            [
                'name' => 'User Manager',
                'password' => 'password',
            ],
        );
        $userManager->syncRoles([$userManagerRole]);

        // 5. Role Manager (только роли и права)
        $roleManager = User::query()->updateOrCreate(
            ['email' => 'rolemanager@example.com'],
            [
                'name' => 'Role Manager',
                'password' => 'password',
            ],
        );
        $roleManager->syncRoles([$roleManagerRole]);

        // 6. Обычный пользователь (без доступа к админке)
        User::query()->updateOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'Regular User',
                'password' => 'password',
            ],
        );

        // 7. Тестовый пользователь (без доступа к админке)
        User::query()->updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => 'password',
            ],
        );

        // 8. Teacher (преподаватель)
        $teacher = User::query()->updateOrCreate(
            ['email' => 'teacher@example.com'],
            [
                'name' => 'Teacher User',
                'password' => 'password',
            ],
        );
        $teacher->syncRoles([$teacherRole]);

        // 9. Student (студент)
        $student = User::query()->updateOrCreate(
            ['email' => 'student@example.com'],
            [
                'name' => 'Student User',
                'password' => 'password',
            ],
        );
        $student->syncRoles([$studentRole]);

        // ============================================
        // СОЗДАНИЕ ТЕСТОВЫХ КУРСОВ
        // ============================================
        if (Course::query()->count() === 0) {
            Course::factory()->count(8)->create([
                'author_id' => $admin->id,
            ]);
        }

        // ============================================
        // СОЗДАНИЕ ТЕСТОВЫХ ЛЕКЦИЙ И ТЕСТОВ
        // ============================================
        $courses = Course::all();
        
        foreach ($courses as $course) {
            // Создаем 3-5 лекций для каждого курса
            for ($i = 1; $i <= rand(3, 5); $i++) {
                $lecture = Lecture::create([
                    'title' => "Лекция {$i}: " . $this->getRandomLectureTitle(),
                    'description' => "Описание лекции {$i} для курса {$course->title}",
                    'content' => $this->getRandomLectureContent(),
                    'status' => 'published',
                    'course_id' => $course->id,
                    'order' => $i,
                ]);

                // Добавляем 1-2 материала к каждой лекции
                for ($j = 1; $j <= rand(1, 2); $j++) {
                    LectureMaterial::create([
                        'title' => "Материал {$j} для лекции {$i}",
                        'file_url' => "/materials/lecture_{$lecture->id}_material_{$j}.pdf",
                        'file_size' => rand(100, 5000) . ' KB',
                        'file_type' => 'PDF',
                        'lecture_id' => $lecture->id,
                    ]);
                }
            }

            // Создаем 1-2 теста для каждого курса
            for ($i = 1; $i <= rand(1, 2); $i++) {
                $test = Test::create([
                    'title' => "Тест {$i} по курсу {$course->title}",
                    'description' => "Проверка знаний по материалам курса",
                    'duration' => rand(20, 60),
                    'difficulty' => ['easy', 'medium', 'hard'][rand(0, 2)],
                    'questions_count' => rand(5, 15),
                    'course_id' => $course->id,
                ]);

                // Создаем вопросы для теста
                for ($j = 1; $j <= $test->questions_count; $j++) {
                    $questionType = ['multiple_choice', 'boolean', 'text'][rand(0, 2)];
                    
                    $question = TestQuestion::create([
                        'question' => "Вопрос {$j} для теста {$i}",
                        'type' => $questionType,
                        'correct_answer' => $this->getCorrectAnswer($questionType),
                        'points' => 1,
                        'test_id' => $test->id,
                    ]);

                    if ($questionType === 'multiple_choice') {
                        $question->update([
                            'options' => json_encode(['Вариант 1', 'Вариант 2', 'Вариант 3', 'Вариант 4']),
                            'correct_answer' => 'Вариант 1',
                        ]);
                    }
                }
            }
        }

        // ============================================
        // ВЫВОД ИНФОРМАЦИИ
        // ============================================
        $this->command->info('=== Тестовые данные для Filament Admin созданы ===');
        $this->command->info('');
        $this->command->info('Пользователи с доступом к админке:');
        $this->command->info('  - superadmin@example.com / password (Super Admin - полный доступ)');
        $this->command->info('  - admin@example.com / password (Admin - полный доступ)');
        $this->command->info('  - content@example.com / password (Content Manager - только курсы)');
        $this->command->info('  - usermanager@example.com / password (User Manager - только пользователи)');
        $this->command->info('  - rolemanager@example.com / password (Role Manager - только роли и права)');
        $this->command->info('');
        $this->command->info('Пользователи без доступа к админке:');
        $this->command->info('  - user@example.com / password');
        $this->command->info('  - test@example.com / password');
        $this->command->info('');
        $this->command->info('Пользователи для Vue приложения:');
        $this->command->info('  - teacher@example.com / password (Teacher - преподаватель)');
        $this->command->info('  - student@example.com / password (Student - студент)');
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
                return 'Вариант 1';
            case 'boolean':
                return 'true';
            case 'text':
                return 'Правильный ответ на текстовый вопрос';
            default:
                return '';
        }
    }
}
