<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class OneHundredUsersSeeder extends Seeder
{
    /**
     * إنشاء 100 مستخدم للتجربة.
     */
    public function run(): void
    {
        $password = Hash::make('password');

        $prefix = 'seed_' . time() . '_';
        for ($i = 1; $i <= 100; $i++) {
            $name = fake('ar_SA')->name();
            $username = $prefix . $i . '_' . Str::random(4);
            $email = $prefix . $i . '@example.com';

            User::create([
                'name' => $name,
                'username' => $username,
                'email' => $email,
                'phone' => null,
                'password' => $password,
                'status' => 'active',
                'is_active' => true,
                'email_verified_at' => now(),
            ]);
        }

        $this->command->info('تم إنشاء 100 مستخدم بنجاح. كلمة المرور الافتراضية: password');
    }
}
