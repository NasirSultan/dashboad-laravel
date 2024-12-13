<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;

class ProjectController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
        ]);

        Project::create([
            'name' => $request->name,
            'price' => $request->price,
        ]);

        return 'Project added successfully!';
    }
}
