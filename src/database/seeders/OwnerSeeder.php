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
        for($i = 1; $i <= 30; $i++){
            DB::table('owners')->insert([
                [
                    'name' => 'test_owner'. $i,
                    'email' => 'owner'.$i.'@owner.com',
                    'password' => Hash::make('password123'),
                    'created_at' => Carbon::now()->subMinute($i),
                ],
            ]);
        }
    }
}
