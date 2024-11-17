<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class static_seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('static_doctors')->insert([
            ['name' => 'ابي صندوق'],
            ['name' => 'محمد سعيد ابو طراب'],
            ['name' => 'ماهر الصارم'],
            ['name' => 'عمار النحاس'],
            ['name' => 'عبدالله العمر'],

        ]);
        DB::table('static_lectures')->insert([
                ['name' => 'برمجة2'],
                ['name' => 'خوارزميات1'],
                ['name' => 'خوارزميات2'],
                ['name' => 'هندسة3'],
                ['name' => 'هندسة نظم المعلومات'],

            ]);
            DB::table('employes')->insert([
        ['name' => 'secrtary','email' => 'secrtary@gmail.com','password' => bcrypt('secrtary123'),'role' => '1'],
        ['name' => 'labman','email' => 'labman@gmail.com','password' => bcrypt('labman123'),'role' => '2'],
        ['name' => 'manger','email' => 'manager@gmail.com','password' => bcrypt('manager123'),'role' => '3'],

              ]);


    }
}
