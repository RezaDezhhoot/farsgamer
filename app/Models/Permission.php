<?php

namespace App\Models;

class Permission extends \Spatie\Permission\Models\Permission
{

    protected static $logAttributes = ['*'];
    protected static $dontLogIfAttributesChangedOnly = ['updated_at'];
    protected static $logOnlyDirty = true;

}
