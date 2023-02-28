<?php

namespace Database\Seeders\Auth;

use Database\Seeders\Traits\DisableForeignKeys;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

/**
 * Class PermissionRoleTableSeeder.
 */
class PermissionRoleTableSeeder extends Seeder
{
    use DisableForeignKeys;

    /**
     * Run the database seed.
     *
     * @return void
     */
    public function run()
    {
        $this->disableForeignKeys();

        // Create Roles
        $admin = Role::create(['name' => config('access.users.admin_role')]);
        $teacher = Role::create(['name' => 'teacher']);
        $student = Role::create(['name' => 'student']);
        $user = Role::create(['name' => 'user']);


        $permissions = [

            ['id' => 1, 'name' => 'user_management_access',],
            ['id' => 2, 'name' => 'user_management_create',],
            ['id' => 3, 'name' => 'user_management_edit',],
            ['id' => 4, 'name' => 'user_management_view',],
            ['id' => 5, 'name' => 'user_management_delete',],

            ['id' => 6, 'name' => 'permission_access',],
            ['id' => 7, 'name' => 'permission_create',],
            ['id' => 8, 'name' => 'permission_edit',],
            ['id' => 9, 'name' => 'permission_view',],
            ['id' => 10, 'name' => 'permission_delete',],

            ['id' => 11, 'name' => 'role_access',],
            ['id' => 12, 'name' => 'role_create',],
            ['id' => 13, 'name' => 'role_edit',],
            ['id' => 14, 'name' => 'role_view',],
            ['id' => 15, 'name' => 'role_delete',],

            ['id' => 16, 'name' => 'user_access',],
            ['id' => 17, 'name' => 'user_create',],
            ['id' => 18, 'name' => 'user_edit',],
            ['id' => 19, 'name' => 'user_view',],
            ['id' => 20, 'name' => 'user_delete',],

            ['id' => 21, 'name' => 'course_access',],
            ['id' => 22, 'name' => 'course_create',],
            ['id' => 23, 'name' => 'course_edit',],
            ['id' => 24, 'name' => 'course_view',],
            ['id' => 25, 'name' => 'course_delete',],

            ['id' => 26, 'name' => 'lesson_access',],
            ['id' => 27, 'name' => 'lesson_create',],
            ['id' => 28, 'name' => 'lesson_edit',],
            ['id' => 29, 'name' => 'lesson_view',],
            ['id' => 30, 'name' => 'lesson_delete',],

            ['id' => 31, 'name' => 'question_access',],
            ['id' => 32, 'name' => 'question_create',],
            ['id' => 33, 'name' => 'question_edit',],
            ['id' => 34, 'name' => 'question_view',],
            ['id' => 35, 'name' => 'question_delete',],

            ['id' => 36, 'name' => 'questions_option_access',],
            ['id' => 37, 'name' => 'questions_option_create',],
            ['id' => 38, 'name' => 'questions_option_edit',],
            ['id' => 39, 'name' => 'questions_option_view',],
            ['id' => 40, 'name' => 'questions_option_delete',],

            ['id' => 41, 'name' => 'test_access',],
            ['id' => 42, 'name' => 'test_create',],
            ['id' => 43, 'name' => 'test_edit',],
            ['id' => 44, 'name' => 'test_view',],
            ['id' => 45, 'name' => 'test_delete',],

            ['id' => 46, 'name' => 'order_access',],
            ['id' => 47, 'name' => 'order_create',],
            ['id' => 48, 'name' => 'order_edit',],
            ['id' => 49, 'name' => 'order_view',],
            ['id' => 50, 'name' => 'order_delete',],

            ['id' => 51, 'name' => 'view backend',], //47

            ['id' => 52, 'name' => 'category_access',], //48
            ['id' => 53, 'name' => 'category_create',],//49
            ['id' => 54, 'name' => 'category_edit',],//50
            ['id' => 55, 'name' => 'category_view',],//51
            ['id' => 56, 'name' => 'category_delete',],//52

            ['id' => 57, 'name' => 'blog_access',], //53
            ['id' => 58, 'name' => 'blog_create',],//54
            ['id' => 59, 'name' => 'blog_edit',],//55
            ['id' => 60, 'name' => 'blog_view',],//56
            ['id' => 61, 'name' => 'blog_delete',],//57

            ['id' => 62, 'name' => 'reason_access',], //58
            ['id' => 63, 'name' => 'reason_create',],//59
            ['id' => 64, 'name' => 'reason_edit',],//60
            ['id' => 65, 'name' => 'reason_view',],//61
            ['id' => 66, 'name' => 'reason_delete',],//62

            ['id' => 67, 'name' => 'page_access',],//63
            ['id' => 68, 'name' => 'page_create',],//64
            ['id' => 69, 'name' => 'page_edit',],//65
            ['id' => 70, 'name' => 'page_view',],//66
            ['id' => 71, 'name' => 'page_delete',],//67

            ['id' => 72, 'name' => 'bundle_access',],//68
            ['id' => 73, 'name' => 'bundle_create',],//69
            ['id' => 74, 'name' => 'bundle_edit',],//70
            ['id' => 75, 'name' => 'bundle_view',],//71
            ['id' => 76, 'name' => 'bundle_delete',],//72

            ['id' => 77, 'name' => 'live_lesson_access'],//73
            ['id' => 78, 'name' => 'live_lesson_create'],//74
            ['id' => 79, 'name' => 'live_lesson_edit'],//75
            ['id' => 80, 'name' => 'live_lesson_view'],//76
            ['id' => 81, 'name' => 'live_lesson_delete'],//77

            ['id' => 82, 'name' => 'live_lesson_slot_access'], //78
            ['id' => 83, 'name' => 'live_lesson_slot_create'],//79
            ['id' => 84, 'name' => 'live_lesson_slot_edit'],//80
            ['id' => 85, 'name' => 'live_lesson_slot_view'],//81
            ['id' => 86, 'name' => 'live_lesson_slot_delete'],//82

            ['id' => 87, 'name' => 'stripe_plan_access'],//83
            ['id' => 88, 'name' => 'stripe_plan_create'],//84
            ['id' => 89, 'name' => 'stripe_plan_edit'],//85
            ['id' => 90, 'name' => 'stripe_plan_view'],//86
            ['id' => 91, 'name' => 'stripe_plan_delete'],//87
            ['id' => 92, 'name' => 'stripe_plan_restore'],//88
        ];

        foreach ($permissions as $item) {
            Permission::create($item);
        }

//        $admin_permissions = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67];

//        $teacher_permissions = [1, 21, 22, 23, 24,25, 26, 27, 28, 29,30, 31, 32, 33, 34,35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 47, 48, 49, 51, 68, 69, 70, 71, 72, 73, 74, 75, 76, 77, 78, 79, 80, 81, 82];
        $teacher_permissions = [1, 21, 22, 23, 24,25, 26, 27, 28, 29,30, 31, 32, 33, 34,35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 51, 52, 53, 55, 72, 73, 74, 75, 76, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86];

        $student_permission = [51];


        $admin->syncPermissions(Permission::all());
        $teacher->syncPermissions($teacher_permissions);
        $student->syncPermissions($student_permission);

        $this->enableForeignKeys();
    }
}
