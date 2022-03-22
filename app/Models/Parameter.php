<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static where(string $string, string $string1)
 * @method static find(int|string $key)
 * @method static updateOrCreate(string[] $array, array $array1)
 * @method static findOrFail($id)
 * @property mixed category_id
 * @property mixed logo
 * @property mixed name
 * @property mixed type
 * @property mixed field
 * @property mixed status
 * @property mixed max
 * @property mixed min
 * @property mixed id
 */
class Parameter extends Model
{
    use HasFactory;

    public function setLogoAttribute($value)
    {
        $this->attributes['logo'] = str_replace(env('APP_URL'), '', $value);
    }

}
