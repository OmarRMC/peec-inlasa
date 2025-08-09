<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    protected function serializeDate(\DateTimeInterface $date)
    {
        // return Carbon::instance($date)->format('d/m/Y H:i');

        Carbon::instance($date)
            ->timezone('America/La_Paz')
            ->format('d/m/Y H:i');
    }
}
