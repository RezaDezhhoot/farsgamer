<?php

namespace App\Models;


use App\Traits\Admin\Searchable;

/**
 * @method static latest(string $string)
 * @method static whereNotIn(string $string, string[] $array)
 * @method static findOrFail($id)
 */
class Role extends \Spatie\Permission\Models\Role
{
    use  Searchable;
    protected static $logAttributes = ['*'];
    protected static $dontLogIfAttributesChangedOnly = ['updated_at'];
    protected static $logOnlyDirty = true;
    protected $searchAbleColumns = ['name'];
    protected $guarded = [];
}
