<?php

namespace Database\Seeders;

use App\Models\Todo;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TodoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       // Membuat beberapa data dummy untuk tabel todos
        Todo::create([
            'title' => 'Finish Laravel Task',
            'assignee' => 'Muhammad Hasan',
            'due_date' => '2025-06-25',
            'time_tracked' => 3,
            'status' => 'open',
            'priority' => 'high',
        ]);

        Todo::create([
            'title' => 'Complete API Documentation',
            'assignee' => 'Ikhsan',
            'due_date' => '2025-06-30',
            'time_tracked' => 5,
            'status' => 'pending',
            'priority' => 'medium',
        ]);

        Todo::create([
            'title' => 'Fix Bug in Authentication',
            'assignee' => 'John Lenon',
            'due_date' => '2025-07-01',
            'time_tracked' => 2,
            'status' => 'in_progress',
            'priority' => 'low',
        ]);
        Todo::create([
            'title' => 'Create a new project',
            'assignee' => 'Agus Setiawan',
            'due_date' => '2025-07-15',
            'time_tracked' => 3,
            'status' => 'completed',
            'priority' => 'low',
        ]);
    }
}
