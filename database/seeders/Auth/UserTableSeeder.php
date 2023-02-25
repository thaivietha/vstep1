<?php

namespace Database\Seeders\Auth;

use App\Models\Auth\User;
use Database\Seeders\Traits\DisableForeignKeys;
use Illuminate\Database\Seeder;

/**
 * Class UserTableSeeder.
 */
class UserTableSeeder extends Seeder
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

        // Add the master administrator, user id of 1
        User::create([
            'first_name'        => 'Admin',
            'last_name'         => 'Istrator',
            'email'             => 'admin@vstep.com',
            'password'          => 'zxcv@1234',
            'confirmation_code' => md5(uniqid(mt_rand(), true)),
            'confirmed'         => true,
        ]);

        User::create([
            'first_name'        => 'Teacher',
            'last_name'         => 'User',
            'email'             => 'teacher@vstep.com',
            'password'          => 'zxcv@1234',
            'confirmation_code' => md5(uniqid(mt_rand(), true)),
            'confirmed'         => true,
        ]);

        User::create([
            'first_name'        => 'Student',
            'last_name'         => 'User',
            'email'             => 'student@vstep.com',
            'password'          => 'zxcv@1234',
            'confirmation_code' => md5(uniqid(mt_rand(), true)),
            'confirmed'         => true,
        ]);

        User::create([
            'first_name'        => 'Normal',
            'last_name'         => 'User',
            'email'             => 'user@vstep.com',
            'password'          => 'zxcv@1234',
            'confirmation_code' => md5(uniqid(mt_rand(), true)),
            'confirmed'         => true,
        ]);

        $this->enableForeignKeys();
    }
}
