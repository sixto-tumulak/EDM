<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Barangay;

class BarangaySeeder extends Seeder
{
    public function run()
    {
        $barangays = [
            'Alegria',
            'Bangbang',
            'Buagsong',
            'Catarman',
            'Cogon',
            'Dapitan',
            'Day-as',
            'Gabi',
            'Gilutongan',
            'Ibabao',
            'Pilipog',
            'Poblacion',
            'San Miguel',
        ];

        foreach ($barangays as $barangay) {
            Barangay::create(['name' => $barangay]);
        }
    }
}