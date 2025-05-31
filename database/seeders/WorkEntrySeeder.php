<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\WorkEntry;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class WorkEntrySeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', User::ROLE_ADMIN)->first();
        $supervisor = User::where('role', User::ROLE_SUPERVISOR)->first();
        $employee1 = User::where('email', 'employee1@example.com')->first();
        $employee2 = User::where('email', 'employee2@example.com')->first();

        if (!$admin || !$supervisor || !$employee1 || !$employee2) {
            $this->command->info('Nie znaleziono wszystkich wymaganych użytkowników (Admin, Supervisor, Employee1, Employee2). Przerywam WorkEntrySeeder.');
            return;
        }

        $creators = [$admin->id, $supervisor->id];
        $employees = [$employee1, $employee2];

        $endDate = Carbon::now();
        $startDate = Carbon::now()->subMonth()->startOfDay();
        $period = CarbonPeriod::create($startDate, $endDate);

        foreach ($employees as $employee) {
            $daysWithEntries = 0;
            foreach ($period as $date) {
                if ($date->isWeekday()) {
                    if (rand(1, 10) <= 8) { // 80% szans na wpis w dzień roboczy
                        WorkEntry::create([
                            'user_id' => $employee->id,
                            'entered_by_user_id' => $creators[array_rand($creators)],
                            'date_of_work' => $date->toDateString(),
                            'hours_worked' => rand(600, 1000) / 100, 
                        ]);
                        $daysWithEntries++;
                    }
                }
                if ($daysWithEntries >= 22 && $employee->id === $employee1->id) break; 
                if ($daysWithEntries >= 20 && $employee->id === $employee2->id) break; 
            }
            
            WorkEntry::create([
                'user_id' => $employee->id,
                'entered_by_user_id' => $supervisor->id,
                'date_of_work' => Carbon::now()->subDays(rand(32,35))->startOfWeek()->addDays(rand(0,4))->toDateString(),
                'hours_worked' => 13.00,
            ]);
        }
        $this->command->info(WorkEntry::count() . ' wpisów czasu pracy zostało utworzonych.');
    }
}