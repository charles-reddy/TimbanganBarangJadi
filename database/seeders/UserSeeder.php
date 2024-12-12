<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // $admin = User::create([
        //     'name' => 'admin',
        //     'email' => 'admin@gmail.com',
        //     'password' => bcrypt('12345678')
        // ]);
        // $admin->assignRole('admin');


        // $penulis = User::create([
        //     'name' => 'penulis',
        //     'email' => 'penulis@gmail.com',
        //     'password' => bcrypt('12345678')
        // ]);
        // $penulis->assignRole('penulis');

        $user  = User::find(15);
        $user->assignRole('operator-timbangan');

        $user1  = User::find(17);
        $user1->assignRole('operator-registrasi');

        $user2  = User::find(18);
        $user2->assignRole('operator-b10');

        $user3  = User::find(20);
        $user3->assignRole('supervisor-timbangan-registrasi');

        $user4  = User::find(22);
        $user4->assignRole('supervisor-b10');

        $user5  = User::find(23);
        $user5->assignRole('manager-logistik');

        
    }
}
