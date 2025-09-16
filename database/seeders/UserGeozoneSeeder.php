<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserGeozoneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // contoh user dengan ID 1 & 2 sudah ada di tabel users
        $user1 = User::find(1); // misalnya "Budi"
        $user2 = User::find(2); // misalnya "Siti"

        // assign user1 ke Kantor Pusat & Bandung
        if ($user1) {
            $user1->geozones()->sync([16, 17]); 
        }

        // assign user2 ke Surabaya saja
        if ($user2) {
            $user2->geozones()->sync([18]); 
        }
    }
}
