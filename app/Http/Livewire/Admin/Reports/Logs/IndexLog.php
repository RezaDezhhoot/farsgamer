<?php

namespace App\Http\Livewire\Admin\Reports\Logs;

use App\Http\Livewire\BaseComponent;
use App\Repositories\Interfaces\ReportRepositoryInterface;
use Livewire\WithPagination;

class IndexLog extends BaseComponent
{
    use WithPagination;
    public function render(ReportRepositoryInterface $reportRepository)
    {
        $this->authorizing('show_reports');
        $reports = $reportRepository->getAll();
        return view('livewire.admin.reports.logs.index-log',['reports' => $reports])
            ->extends('livewire.admin.layouts.admin');
    }
}
