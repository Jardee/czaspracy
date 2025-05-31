<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Models\WorkEntry;

// nie działało mi wbudowane WhereDate to coś takiego powstało
class UniqueWorkEntryForUserAndDate implements Rule
{
    protected $userId;
    protected $dateOfWork;
    protected $ignoreId;

    public function __construct($userId, $dateOfWork, $ignoreId = null)
    {
        $this->userId = $userId;
        $this->dateOfWork = $dateOfWork;
        $this->ignoreId = $ignoreId;
    }

    public function passes($attribute, $value)
    {
        $query = WorkEntry::where('user_id', $this->userId)
                          ->whereDate('date_of_work', $this->dateOfWork);

        if ($this->ignoreId) {
            $query->where('id', '!=', $this->ignoreId);
        }

        return !$query->exists();
    }

    public function message()
    {
        return 'Dla tego pracownika istnieje już wpis w wybranym dniu.';
    }
}