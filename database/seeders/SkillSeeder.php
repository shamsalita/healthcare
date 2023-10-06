<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SkillSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
        $date = date('Y-m-d H:i:s');
        DB::table('skills')->insert([
            ['name'=>'Neurosurgeon','created_at'=>$date,'updated_at'=>$date],
            ['name'=>'Eurosurgeon','created_at'=>$date,'updated_at'=>$date],
            ['name'=>'Cardiologists','created_at'=>$date,'updated_at'=>$date],
            ['name'=>'Endoscopy','created_at'=>$date,'updated_at'=>$date],
            ['name' => 'General Suregon','created_at'=>$date,'updated_at'=>$date],
            ['name' => 'Orthopedics','created_at'=>$date,'updated_at'=>$date],
            ['name' => 'Gynecologist','created_at'=>$date,'updated_at'=>$date],
            ['name' => 'Dermatology','created_at'=>$date,'updated_at'=>$date],
            ['name' => 'Pediatrics','created_at'=>$date,'updated_at'=>$date],
            ['name' => 'ENT','created_at'=>$date,'updated_at'=>$date],
        ]);
    }
}
