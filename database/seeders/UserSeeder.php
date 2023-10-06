<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Dirape\Token\Token;


class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       
        /* $user = new User();
        $user->first_name = "Hitesh";
        $user->last_name = "P";
        $user->hospital_name = "civil hospital";
        $user->email = "user@gmail.com";
        $user->password = Hash::make('password');
        $user->mobile_number = "9865849658";
        $user->residential_city = "Surat";
        $user->residential_country = "INDIA";
        $user->remember_token = (new Token())->Unique('users', 'remember_token', 60);
        $user->save(); */

        $user = new User();
        $user->name = "Hitesh";
        $user->email = "user@gmail.com";
        $user->password = bcrypt('password');
        $user->phone_number = "9865849658";
        $user->address = "Surat";
        $user->remember_token = (new Token())->Unique('users', 'remember_token', 60);
        $user->save();
    }
}
