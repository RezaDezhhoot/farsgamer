<?php


namespace App\Repositories\Classes;

use App\Models\Report;
use App\Repositories\Interfaces\ReportRepositoryInterface;


class ReportRepository implements ReportRepositoryInterface
{

    public function getAll()
    {
        // TODO: Implement getAll() method.
        return Report::paginate(12);
    }
}
