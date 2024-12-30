<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class OwnerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('owners')->insert([
            [
                'name' => 'test_owner1',
                'email' => 'owner1@owner.com',
                'password' => Hash::make('password123'),
                'created_at' => Carbon::now(),
            ],
            [
                'name' => 'test_owner2',
                'email' => 'owner2@owner.com',
                'password' => Hash::make('password123'),
                'created_at' => Carbon::now()->subMinute(),
            ],
            [
                'name' => 'test_owner3',
                'email' => 'owner3@owner.com',
                'password' => Hash::make('password123'),
                'created_at' => Carbon::now()->subHour(),
            ]
        ]);
    }
}
