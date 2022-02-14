<?php

namespace App\Traits\Admin;

trait Searchable
{
    public function scopeSearch($query, $search)
    {
        if ($search){
            foreach ($this->searchAbleColumns as $index => $column) {
                if ($index == 0) {
                    $query->where($column, 'LIKE', "%$search%");
                    continue;
                }

                $query->orWhere($column, 'LIKE', "%$search%");
            }
        }

        return $query;
    }
}
