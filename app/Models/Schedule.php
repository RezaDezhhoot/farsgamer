<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static updateOrCreate(array $array, array $array1)
 */
class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'saturday','sunday','monday','tuesday','wednesday','thursday','friday','user_id',
    ];
}
