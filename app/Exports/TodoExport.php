<?php

namespace App\Exports;

use App\Models\Todo;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class TodoExport implements FromCollection, WithHeadings
{
    public $filtered;

    public function __construct($filtered)
    {
        $this->filtered = $filtered;
    }

    public function collection()
    {
        $data = $this->filtered->map(function($todo) {
            return [
                $todo->title,
                $todo->assignee,
                $todo->due_date,
                $todo->time_tracked,
                $todo->status,
                $todo->priority,
            ];
        });

        $data->push(['']);
        $data->push(['SUMMARY', '', '', '', '', '']);
        $data->push(['Total Todos', count($this->filtered), '', '', '', '']);
        $data->push(['Total Time Tracked', $this->filtered->sum('time_tracked') . ' hours', '', '', '', '']);

        return $data;
    }

    public function headings(): array
    {
        return ['Title', 'Assignee', 'Due Date', 'Time Tracked', 'Status', 'Priority'];
    }


}
