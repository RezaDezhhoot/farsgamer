<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static where(string $string, $id)
 */
class OrderParameter extends Model
{
    use HasFactory;

    protected $table = 'orders_has_parameters';

    public function parameter()
    {
        return $this->belongsTo(Parameter::class);
    }

}
