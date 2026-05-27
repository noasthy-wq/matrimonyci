<?php

namespace Database\Seeders;

use App\Models\TermsAcceptance;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TermsAcceptancesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::all()->each(function ($user) {
            TermsAcceptance::create([
                'user_id' => $user->id,
                'version' => '1.0',
                'accepted_at' => now(),
                'ip_address' => '127.0.0.1',
            ]);
        });
    }
}