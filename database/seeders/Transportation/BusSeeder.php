<?php

namespace Database\Seeders\Transportation;

use Illuminate\Database\Seeder;
use App\Models\Transportation\Bus;
use App\Enums\Status;

class BusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $buses = [
            [
                'area_name' => 'Downtown Route',
                'bus_number' => 'BUS-001',
                'capacity' => 40,
                'driver_name' => 'Ahmed Hassan',
                'driver_phone' => '+973-3333-1111',
                'license_plate' => 'BH-12345',
                'status' => Status::ACTIVE,
                'branch_id' => 1,
            ],
            [
                'area_name' => 'Northern Area',
                'bus_number' => 'BUS-002',
                'capacity' => 35,
                'driver_name' => 'Mohammed Ali',
                'driver_phone' => '+973-3333-2222',
                'license_plate' => 'BH-23456',
                'status' => Status::ACTIVE,
                'branch_id' => 1,
            ],
            [
                'area_name' => 'Southern District',
                'bus_number' => 'BUS-003',
                'capacity' => 45,
                'driver_name' => 'Khalid Ibrahim',
                'driver_phone' => '+973-3333-3333',
                'license_plate' => 'BH-34567',
                'status' => Status::ACTIVE,
                'branch_id' => 1,
            ],
            [
                'area_name' => 'Eastern Suburbs',
                'bus_number' => 'BUS-004',
                'capacity' => 30,
                'driver_name' => 'Youssef Ahmed',
                'driver_phone' => '+973-3333-4444',
                'license_plate' => 'BH-45678',
                'status' => Status::INACTIVE,
                'branch_id' => 1,
            ],
        ];

        foreach ($buses as $bus) {
            Bus::create($bus);
        }
    }
}
