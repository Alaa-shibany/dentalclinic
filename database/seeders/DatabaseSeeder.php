<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Admin;
use App\Models\Attachment;
use App\Models\Doctor;
use App\Models\GalleryPiece;
use App\Models\Order;
use App\Models\Tooth;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Admin::factory(10)->create();
        Doctor::factory(20)
            ->has(
                Order::factory(random_int(1,5))
                    ->has(Tooth::factory(random_int(5,10)))
                    ->has(Attachment::factory(4))
            )->create();
        Admin::create([
            'username' => 'admin',
            'password' => '12345',
            'super' => true
        ]);
        $d=Doctor::factory()->has(
            Order::factory(random_int(1,5))
                ->has(Tooth::factory(random_int(5,10)))
        )->create([
            'phone' => '+963993596032'
        ]);
        $d->phone_verified_at=now();
        $d->save();
        GalleryPiece::factory(4)->create();
    }
}
