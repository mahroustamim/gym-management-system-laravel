<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    private $permissions = [
        ['عرض السجلات', 'web-system'],
        ['عرض التقارير', 'web-system'],
        ['عرض عمليات الدفع', 'web-system'],
        ['إدارة أكواد الخصم', 'web-system'],
        ['تعديل الاعدادات', 'web-system'],

        ['إضافة جيم', 'web-system'],
        ['تعديل جيم', 'web-system'],
        ['حذف جيم', 'web-system'],
        ['عرض جيم', 'web-system'],

        ['إضافة موظفين', 'web-system'],
        ['تعديل موظفين', 'web-system'],
        ['حذف موظفين', 'web-system'],
        ['عرض موظفين', 'web-system'],

        ['إضافة خطة اشتراك', 'web-system'],
        ['تعديل خطة اشتراك', 'web-system'],
        ['حذف خطة اشتراك', 'web-system'],
        ['عرض خطة اشتراك', 'web-system'],


        // ================== Gym Permissions ==================

        ['تعديل بيانات الجيم', 'web-gym'],
        ['عرض بيانات الجيم', 'web-gym'],
        ['عرض السجلات', 'web-gym'],
        ['عرض التقارير', 'web-gym'],
        ['تسجيل الحضور', 'web-gym'],


        ['إضافة موظفين', 'web-gym'],
        ['تعديل موظفين', 'web-gym'],
        ['حذف موظفين', 'web-gym'],
        ['عرض موظفين', 'web-gym'],

        ['إضافة مدربين', 'web-gym'],
        ['تعديل مدربين', 'web-gym'],
        ['حذف مدربين', 'web-gym'],
        ['عرض مدربين', 'web-gym'],

        ['إضافة أعضاء', 'web-gym'],
        ['تعديل أعضاء', 'web-gym'],
        ['حذف أعضاء', 'web-gym'],
        ['عرض أعضاء', 'web-gym'],

        ['إضافة خطة اشتراك', 'web-gym'],
        ['تعديل خطة اشتراك', 'web-gym'],
        ['حذف خطة اشتراك', 'web-gym'],
        ['عرض خطة اشتراك', 'web-gym'],

    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->permissions as [$name, $guard]) {
            Permission::firstOrCreate(
                [
                    'name' => $name,
                    'guard_name' => $guard,
                ]
            );
        }
    }
}
