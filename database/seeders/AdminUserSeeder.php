<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
  public function run(): void
    {
        // 1. إنشاء/الحصول على دور المدير
        $adminRole = Role::firstOrCreate(['name' => 'admin']);

        // 2. منح جميع الصلاحيات لدور المدير
        $permissions = Permission::all();
        $adminRole->syncPermissions($permissions);

        // 3. إنشاء/البحث عن مستخدم مدير افتراضي
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'مدير النظام',
                'password' => Hash::make('123456789'),
                'email_verified_at' => now(),
            ]
        );

        // 4. تعيين دور المدير للمستخدم
        $adminUser->assignRole($adminRole);

        // 5. إعطاء المستخدم كل الصلاحيات مباشرة (لضمان الوصول الكامل)
        $adminUser->givePermissionTo(Permission::all());

        // إنشاء دور مستخدم عادي
        $userRole = Role::firstOrCreate(['name' => 'user']);

        // منح صلاحيات محدودة للمستخدم العادي
        $userPermissions = [
            'dashboard-view',
            'user-show', // يمكنه رؤية ملفه الشخصي فقط
        ];

        $userRole->syncPermissions($userPermissions);
    }
}
