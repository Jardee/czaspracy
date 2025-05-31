<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WorkEntry;
use App\Models\User;
use App\Models\Comment;

class CommentSeeder extends Seeder
{
    public function run(): void
    {
        $allWorkEntries = WorkEntry::all();

        if ($allWorkEntries->isEmpty()) {
            $this->command->info('Brak wpisów czasu pracy do skomentowania. Przerywam CommentSeeder.');
            return;
        }

        $admin = User::where('role', User::ROLE_ADMIN)->first();
        $supervisor = User::where('role', User::ROLE_SUPERVISOR)->first();
        $allEmployees = User::where('role', User::ROLE_EMPLOYEE)->get();

        $possibleCommenters = [];
        if ($admin) $possibleCommenters[] = $admin;
        if ($supervisor) $possibleCommenters[] = $supervisor;
        foreach ($allEmployees as $emp) {
            $possibleCommenters[] = $emp;
        }

        if (empty($possibleCommenters)) {
            $this->command->info('Brak użytkowników do dodawania komentarzy. Przerywam CommentSeeder.');
            return;
        }

        $commentTexts = [
            'Wszystko zgodnie z planem.',
            'Zadania wykonane na czas.',
            'Potwierdzam godziny.',
            'Mała uwaga: ... (do uzupełnienia).',
            'Dobra robota!',
            'Proszę o kontakt w sprawie tego dnia.',
            'Sprawdzone i zaakceptowane.',
            'OK.',
            'Bez uwag.',
            'Wymaga dodatkowej weryfikacji.',
        ];

        foreach ($allWorkEntries as $entry) {
            $numberOfComments = rand(1, 3); // Od 1 do 3 komentarzy na wpis

            for ($i = 0; $i < $numberOfComments; $i++) {
                $commenter = $possibleCommenters[array_rand($possibleCommenters)];
                
                if ($commenter->role === User::ROLE_EMPLOYEE && $commenter->id !== $entry->user_id && rand(1,5) > 1) { 
                    // znaleźć innego komentującego jeśli pracownik wylosował nie swój wpis
                    if ($supervisor) $commenter = $supervisor;
                    else if ($admin) $commenter = $admin;
                    else continue; 
                }


                Comment::create([
                    'work_entry_id' => $entry->id,
                    'user_id' => $commenter->id,
                    'comment_text' => $commentTexts[array_rand($commentTexts)] . " (dla wpisu z dnia: " . $entry->date_of_work->format('Y-m-d') . " pracownika ID: ".$entry->user_id.")",
                ]);
            }
        }
        $this->command->info(Comment::count() . ' komentarzy zostało utworzonych.');
    }
}