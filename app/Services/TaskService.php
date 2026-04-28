<?php

namespace App\Services;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskService
{
    public function store(Request $request)
    {
        $data = $request->validated();
        return Task::create([
            'user_id' => $request->user()->id,
            'title' => $data['title'],
            'description' => $data['description'],
            'status' => $data['status'],
        ]);
    }
}
